<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisSurat;
use App\Models\ApprovalFlow;
use App\Models\Kelurahan;

class SkpSeeder extends Seeder
{
    /**
     * Seed jenis surat SKP + approval flow untuk semua kelurahan
     * di kecamatan Landasan Ulin (kecamatan_id 6372010).
     */
    public function run(): void
    {
        // ---- 1. JenisSurat ----
        $skp = JenisSurat::updateOrCreate(
            ['kode' => 'SKP'],
            [
                'nama'      => 'Surat Keterangan Penghasilan',
                'deskripsi' => 'Surat keterangan yang menyatakan jumlah penghasilan pemohon, biasanya untuk keperluan administrasi perbankan, beasiswa, atau bantuan.',
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
                    'no_wa',
                    'jumlah_penghasilan',
                    'keperluan',
                    // Surat pengantar RT/RW
                    'rt',
                    'rw',
                    'no_surat_pengantar',
                    'tanggal_surat_pengantar',
                    // Dokumen lampiran
                    'skp_surat_pengantar',
                    'skp_blangko_pernyataan',
                    'skp_ktp_kk',
                    'skp_ktp_saksi',
                    'skp_bukti_pbb',
                ]),
                'is_active' => true,
            ]
        );

        $this->command->info("✓ JenisSurat SKP (ID: {$skp->id}) siap.");

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
                    'jenis_surat_id' => $skp->id,
                    'kelurahan_id'   => $kelurahan->id,
                ],
                [
                    'nama'                       => 'Flow Kelurahan SKP',
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

        $this->command->info('✓ SkpSeeder selesai.');
    }
}
