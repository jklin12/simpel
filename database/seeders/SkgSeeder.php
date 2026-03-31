<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisSurat;
use App\Models\ApprovalFlow;
use App\Models\Kelurahan;

class SkgSeeder extends Seeder
{
    /**
     * Seed jenis surat SKG + approval flow untuk semua kelurahan
     * di kecamatan Landasan Ulin (kecamatan_id 6372010).
     */
    public function run(): void
    {
        // ---- 1. JenisSurat ----
        $skg = JenisSurat::updateOrCreate(
            ['kode' => 'SKG'],
            [
                'nama'      => 'Surat Keterangan Gaib',
                'deskripsi' => 'Surat Pengantar / Keterangan Gaib yang diterbitkan oleh Kelurahan.',
                'template_path' => null, // akan dihandle secara khusus dengan blade pdf
                'required_fields' => [
                    // Data Pemohon
                    'nama_lengkap',
                    'nik_bersangkutan',
                    'no_wa',
                    'jenis_kelamin',
                    'agama',
                    'tempat_lahir',
                    'tanggal_lahir',
                    'pekerjaan',
                    'alamat_lengkap',
                    'keperluan',

                    // Data Orang Gaib
                    'gaib_nama',
                    'gaib_nik',
                    'gaib_jenis_kelamin',
                    'gaib_agama',
                    'gaib_tempat_lahir',
                    'gaib_tanggal_lahir',
                    'gaib_pekerjaan',
                    'gaib_alamat',

                    // Surat Pengantar RT/RW
                    'rt',
                    'rw',
                    'no_surat_pengantar',
                    'tanggal_surat_pengantar',

                    // Surat Pernyataan
                    'tanggal_surat_pernyataan',

                    // Lampiran SKG
                    'skg_surat_pengantar_rtrw',
                    'skg_blangko_pernyataan',
                    'skg_ktp_kk_bersangkutan',
                    'skg_ktp_saksi',
                    'skg_bukti_lunas_pbb',
                ],
                'is_active' => true,
            ]
        );

        $this->command->info("✓ JenisSurat SKG (ID: {$skg->id}) siap.");

        // ---- 2. Approval Flow ----
        // Buat flow untuk setiap kelurahan di kecamatan Landasan Ulin
        $kelurahans = Kelurahan::where('kecamatan_id', '6372010')->get();

        if ($kelurahans->isEmpty()) {
            $this->command->warn('⚠ Tidak ada kelurahan ditemukan untuk kecamatan_id 6372010. Jalankan MasterLocationSeeder terlebih dahulu.');
            return;
        }

        foreach ($kelurahans as $kelurahan) {
            $flow = ApprovalFlow::updateOrCreate(
                [
                    'jenis_surat_id' => $skg->id,
                    'kelurahan_id'   => $kelurahan->id,
                ],
                [
                    'nama'                       => 'Flow Kelurahan SKG',
                    'require_kecamatan_approval' => false,
                    'require_kabupaten_approval' => false,
                    'is_active'                  => true,
                ]
            );

            // Hapus steps lama agar tidak duplikat saat re-seed
            $flow->steps()->delete();

            // Step 1 — Verifikasi Admin Kelurahan
            $flow->steps()->create([
                'step_order'  => 1,
                'role_name'   => 'admin_kelurahan',
                'step_name'   => 'Verifikasi Berkas oleh Admin Kelurahan',
                'is_required' => true,
            ]);

            $this->command->info("  ↳ Flow untuk Kelurahan {$kelurahan->nama} (ID: {$kelurahan->id}) dibuat.");
        }

        $this->command->info('✓ SkgSeeder selesai.');
    }
}
