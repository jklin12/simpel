<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisSurat;
use App\Models\ApprovalFlow;
use App\Models\Kelurahan;

class SksiSeeder extends Seeder
{
    /**
     * Seed jenis surat SKSI + approval flow untuk semua kelurahan
     * di kecamatan Landasan Ulin (kecamatan_id 6372010).
     */
    public function run(): void
    {
        // ---- 1. JenisSurat ----
        $sksi = JenisSurat::updateOrCreate(
            ['kode' => 'SKSI'],
            [
                'nama'      => 'Surat Keterangan Suami Istri',
                'deskripsi' => 'Surat Pengantar / Keterangan Suami Istri yang diterbitkan oleh Kelurahan.',
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

                    // Data Istri
                    'istri_nama',
                    'istri_nik',
                    'istri_jenis_kelamin',
                    'istri_agama',
                    'istri_tempat_lahir',
                    'istri_tanggal_lahir',
                    'istri_pekerjaan',
                    'istri_alamat',

                    // Surat Pengantar RT/RW
                    'rt',
                    'rw',
                    'no_surat_pengantar',
                    'tanggal_surat_pengantar',

                    // Surat Pernyataan
                    'tanggal_surat_pernyataan',

                    // Lampiran SKSI
                    'sksi_surat_pengantar_rtrw',
                    'sksi_blangko_pernyataan',
                    'sksi_ktp_kk_bersangkutan',
                    'sksi_ktp_saksi',
                    'sksi_bukti_lunas_pbb',
                ],
                'is_active' => true,
            ]
        );

        $this->command->info("✓ JenisSurat SKSI (ID: {$sksi->id}) siap.");

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
                    'jenis_surat_id' => $sksi->id,
                    'kelurahan_id'   => $kelurahan->id,
                ],
                [
                    'nama'                       => 'Flow Kelurahan SKSI',
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

        $this->command->info('✓ SksiSeeder selesai.');
    }
}
