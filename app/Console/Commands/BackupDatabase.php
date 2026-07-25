<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

/**
 * Backup database MySQL: dump via `mysqldump` lalu kompres gzip, disimpan ke
 * disk privat (storage/app/backups/db/{type}). Menyimpan hanya sejumlah file
 * terbaru per jenis (lihat config `backup.keep`, default 1) demi hemat storage.
 *
 * Kredensial DB tidak dilewatkan lewat argumen CLI (agar tidak bocor di daftar
 * proses) — memakai temporary `defaults-extra-file`.
 *
 * Pemakaian:
 *   php artisan db:backup                 # backup harian (default)
 *   php artisan db:backup --type=weekly   # backup mingguan
 */
class BackupDatabase extends Command
{
    protected $signature = 'db:backup {--type=daily : Jenis backup: daily atau weekly}';

    protected $description = 'Backup database MySQL (dump + gzip) ke storage privat, dengan prune otomatis';

    private const CHUNK = 1024 * 512; // 512KB per baca saat kompres

    public function handle(): int
    {
        $type = strtolower((string) $this->option('type'));
        if (!in_array($type, ['daily', 'weekly'], true)) {
            $this->error("Jenis tidak valid: '{$type}'. Gunakan 'daily' atau 'weekly'.");

            return self::FAILURE;
        }

        $conn = config('database.connections.mysql');
        if (empty($conn['database'])) {
            $this->error('Konfigurasi database MySQL tidak ditemukan.');

            return self::FAILURE;
        }

        $disk    = Storage::disk(config('backup.disk', 'local'));
        $dir     = "backups/db/{$type}";
        $disk->makeDirectory($dir); // no-op bila sudah ada

        $baseDir  = rtrim($disk->path($dir), '/\\');
        $stamp    = now()->format('Y-m-d_His');
        $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $conn['database']);
        $sqlPath  = $baseDir.DIRECTORY_SEPARATOR.$safeName.'_'.$stamp.'.sql';
        $gzPath   = $sqlPath.'.gz';

        $cnfPath = $this->writeDefaultsFile($conn);

        try {
            $this->dump($conn, $cnfPath, $sqlPath);
            $this->compress($sqlPath, $gzPath);
        } catch (\Throwable $e) {
            @unlink($sqlPath);
            @unlink($gzPath);
            Log::error('Backup database gagal', ['type' => $type, 'error' => $e->getMessage()]);
            $this->error('Backup gagal: '.$e->getMessage());

            return self::FAILURE;
        } finally {
            @unlink($cnfPath);
            @unlink($sqlPath); // .sql sementara — hasil final hanya .gz
        }

        $sizeMb = round(filesize($gzPath) / 1048576, 2);
        $pruned = $this->prune($disk, $dir, $gzPath);

        Log::info('Backup database sukses', [
            'type'  => $type,
            'file'  => $gzPath,
            'sizeMb' => $sizeMb,
            'pruned' => $pruned,
        ]);

        $this->info("Backup {$type} sukses: ".basename($gzPath)." ({$sizeMb} MB)");
        if ($pruned > 0) {
            $this->line("Menghapus {$pruned} backup lama.");
        }

        return self::SUCCESS;
    }

    /**
     * Tulis temporary defaults-extra-file berisi kredensial (mode 0600).
     */
    private function writeDefaultsFile(array $conn): string
    {
        $path = tempnam(sys_get_temp_dir(), 'dbcnf_');

        $lines = [
            '[client]',
            'host='.($conn['host'] ?? '127.0.0.1'),
            'port='.($conn['port'] ?? '3306'),
            'user='.($conn['username'] ?? ''),
            'password="'.str_replace('"', '\"', (string) ($conn['password'] ?? '')).'"',
        ];

        file_put_contents($path, implode("\n", $lines)."\n");
        @chmod($path, 0600);

        return $path;
    }

    /**
     * Jalankan mysqldump, arahkan output ke file .sql.
     */
    private function dump(array $conn, string $cnfPath, string $sqlPath): void
    {
        $command = [
            config('backup.mysqldump_path', 'mysqldump'),
            '--defaults-extra-file='.$cnfPath,
            '--single-transaction',
            '--quick',
            '--no-tablespaces',
            '--routines',
            '--events',
            $conn['database'],
        ];

        $out = fopen($sqlPath, 'wb');
        if ($out === false) {
            throw new \RuntimeException("Tidak bisa menulis file dump: {$sqlPath}");
        }

        try {
            $process = new Process($command);
            $process->setTimeout(1800); // 30 menit
            $process->run(function ($typeOut, $buffer) use ($out) {
                if ($typeOut === Process::OUT) {
                    fwrite($out, $buffer);
                }
            });
        } finally {
            fclose($out);
        }

        if (!$process->isSuccessful()) {
            throw new \RuntimeException('mysqldump error: '.trim($process->getErrorOutput()));
        }

        if (!file_exists($sqlPath) || filesize($sqlPath) === 0) {
            throw new \RuntimeException('Hasil dump kosong.');
        }
    }

    /**
     * Kompres file .sql -> .sql.gz pakai stream (lintas-platform, tanpa binary gzip).
     */
    private function compress(string $sqlPath, string $gzPath): void
    {
        $in = fopen($sqlPath, 'rb');
        if ($in === false) {
            throw new \RuntimeException("Tidak bisa membaca dump: {$sqlPath}");
        }

        $gz = gzopen($gzPath, 'wb9');
        if ($gz === false) {
            fclose($in);
            throw new \RuntimeException("Tidak bisa membuat file gz: {$gzPath}");
        }

        try {
            while (!feof($in)) {
                $chunk = fread($in, self::CHUNK);
                if ($chunk === false) {
                    throw new \RuntimeException('Gagal membaca chunk dump.');
                }
                gzwrite($gz, $chunk);
            }
        } finally {
            gzclose($gz);
            fclose($in);
        }
    }

    /**
     * Sisakan hanya `backup.keep` file terbaru pada folder, hapus sisanya.
     * File yang baru dibuat ($keepPath) selalu dipertahankan.
     *
     * @return int jumlah file yang dihapus
     */
    private function prune($disk, string $dir, string $keepPath): int
    {
        $keep = max(1, (int) config('backup.keep', 1));

        // Semua .gz di folder, urut terbaru dulu berdasarkan waktu modifikasi.
        $files = collect($disk->files($dir))
            ->filter(fn ($f) => str_ends_with($f, '.gz'))
            ->sortByDesc(fn ($f) => $disk->lastModified($f))
            ->values();

        $deleted = 0;
        $keepBase = basename($keepPath);

        foreach ($files as $index => $file) {
            // Pertahankan file terbaru sejumlah $keep; jangan pernah hapus file baru.
            if ($index < $keep || basename($file) === $keepBase) {
                continue;
            }
            $disk->delete($file);
            $deleted++;
        }

        return $deleted;
    }
}
