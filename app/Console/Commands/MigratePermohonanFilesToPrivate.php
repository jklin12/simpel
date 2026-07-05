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

        $copied  = 0;   // baru tersalin utuh
        $skipped = 0;   // sudah tersalin utuh sebelumnya
        $failed  = 0;   // gagal salin utuh (mis. disk penuh) — sumber dipertahankan

        foreach ($this->directories as $dir) {
            if (!$public->exists($dir)) {
                $this->line("Lewati '{$dir}' — tidak ada di disk public.");
                continue;
            }

            foreach ($public->allFiles($dir) as $file) {
                $srcSize = $public->size($file);

                // Sudah ada salinan UTUH di local (verifikasi ukuran, bukan sekadar exists).
                if ($local->exists($file) && $local->size($file) === $srcSize) {
                    $skipped++;
                    if ($delete) {
                        // Aman dihapus dari public — membebaskan ruang duplikat.
                        $public->delete($file);
                    }
                    continue;
                }

                // (Re)salin — menimpa salinan parsial jika ada.
                $local->put($file, $public->get($file));

                // Verifikasi hasil salin; JANGAN hapus sumber kalau tidak utuh.
                if (!$local->exists($file) || $local->size($file) !== $srcSize) {
                    $this->error("GAGAL  {$file} — salinan tidak utuh, sumber public DIPERTAHANKAN.");
                    $failed++;
                    continue;
                }

                $this->info("MOVE   {$file}");
                $copied++;

                if ($delete) {
                    $public->delete($file);
                }
            }
        }

        $this->newLine();
        $this->info("Selesai. Baru tersalin: {$copied}, sudah ada: {$skipped}, gagal: {$failed}.");
        if ($delete) {
            $this->line('Mode --delete: sumber di disk public dihapus HANYA setelah salinan terverifikasi utuh.');
        } else {
            $this->warn('Mode salin (tanpa --delete): file kini ADA DI DUA disk (public + local) → footprint 2x.');
            $this->warn('Untuk disk terbatas, jalankan dengan --delete agar pindah file-per-file (peak ruang minimal).');
        }
        if ($failed > 0) {
            $this->warn("Ada {$failed} file gagal (kemungkinan disk penuh). Bebaskan ruang lalu jalankan ulang command ini dengan --delete.");
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
