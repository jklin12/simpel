<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisSurat;
use App\Models\ApprovalFlow;
use App\Models\Kelurahan;

class SktmrSeeder extends Seeder
{
    /**
     * Seed jenis surat SKTMR + approval flow untuk semua kelurahan
     * di kecamatan Landasan Ulin (kecamatan_id 6372010).
     */
    public function run(): void
    {
        // ---- 1. JenisSurat ----
        $sktmr = JenisSurat::updateOrCreate(
            ['kode' => 'SKTMR'],
            [
                'nama'      => 'Surat Keterangan Tidak Memiliki Rumah',
                'deskripsi' => 'Surat keterangan yang menyatakan bahwa pemohon tidak memiliki rumah sendiri, untuk keperluan administrasi perumahan, beasiswa, atau bantuan hunian.',
                'template_path' => null,
                'required_fields' => json_encode([
                    // Data diri
                    'nama_lengkap',
                    'nik_bersangkutan',
                    'jenis_kelamin',
                    'agama',
                    'tempat_lahir',
                    'tanggal_lahir',
                    'status_perkawinan',
                    'pekerjaan',
                    'pendidikan_terakhir',
                    'alamat_lengkap',
                    'keperluan',
                    // Surat pengantar RT/RW
                    'rt',
                    'rw',
                    'no_surat_pengantar',
                    'tanggal_surat_pengantar',
                    // Surat pernyataan
                    'no_surat_pernyataan',
                    'tanggal_surat_pernyataan',
                    // Dokumen lampiran
                    'sktmr_surat_pengantar',
                    'sktmr_blangko_pernyataan',
                    'sktmr_ktp_kk',
                    'sktmr_ktp_saksi',
                    'sktmr_bukti_pbb',
                ]),
                'is_active' => true,
            ]
        );

        $this->command->info("✓ JenisSurat SKTMR (ID: {$sktmr->id}) siap.");

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
                    'jenis_surat_id' => $sktmr->id,
                    'kelurahan_id'   => $kelurahan->id,
                ],
                [
                    'nama'                       => 'Flow Kelurahan SKTMR',
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

        $this->command->info('✓ SktmrSeeder selesai.');
    }
}
