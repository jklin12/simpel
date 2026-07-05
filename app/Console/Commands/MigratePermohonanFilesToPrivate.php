<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Memindahkan dokumen PII permohonan & surat final yang sudah ditandatangani
 * dari disk `public` (web-accessible via /storage) ke disk `local` (privat).
 *
 * Latar belakang: security patch — KTP/KK/buku nikah/surat TTD berisi PII
 * tidak boleh dapat diunduh tanpa autentikasi lewat URL /storage/...
 *
 * Pemakaian:
 *   php artisan permohonan:migrate-files            # salin (dry-run aman, tidak hapus sumber)
 *   php artisan permohonan:migrate-files --delete   # salin lalu hapus dari disk public
 */
class MigratePermohonanFilesToPrivate extends Command
{
    protected $signature = 'permohonan:migrate-files {--delete : Hapus file dari disk public setelah berhasil disalin}';

    protected $description = 'Pindahkan dokumen PII permohonan & surat TTD dari disk public ke disk privat (local)';

    /**
     * Direktori yang dipindahkan (relatif terhadap root disk).
     */
    private array $directories = ['permohonan', 'surat-selesai'];

    public function handle(): int
    {
        $public = Storage::disk('public');
        $local  = Storage::disk('local');
        $delete = (bool) $this->option('delete');

        $copied = 0;
        $skipped = 0;

        foreach ($this->directories as $dir) {
            if (!$public->exists($dir)) {
                $this->line("Lewati '{$dir}' — tidak ada di disk public.");
                continue;
            }

            foreach ($public->allFiles($dir) as $file) {
                if ($local->exists($file)) {
                    $this->warn("SKIP  {$file} — sudah ada di disk local.");
                    $skipped++;

                    if ($delete) {
                        $public->delete($file);
                    }
                    continue;
                }

                $local->put($file, $public->get($file));
                $this->info("COPY  {$file}");
                $copied++;

                if ($delete) {
                    $public->delete($file);
                }
            }
        }

        $this->newLine();
        $this->info("Selesai. Disalin: {$copied}, dilewati: {$skipped}." . ($delete ? ' Sumber di disk public dihapus.' : ' Sumber di disk public DIBIARKAN (jalankan dengan --delete untuk menghapus).'));

        return self::SUCCESS;
    }
}
