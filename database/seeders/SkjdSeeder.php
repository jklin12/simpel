<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisSurat;
use App\Models\ApprovalFlow;
use App\Models\Kelurahan;

class SkjdSeeder extends Seeder
{
    /**
     * Seed jenis surat SKJD + approval flow untuk semua kelurahan
     * di kecamatan Landasan Ulin (kecamatan_id 6372010).
     */
    public function run(): void
    {
        // ---- 1. JenisSurat ----
        $skjd = JenisSurat::updateOrCreate(
            ['kode' => 'SKJD'],
            [
                'nama'      => 'Surat Keterangan Janda/Duda',
                'deskripsi' => 'Surat Pengantar / Keterangan Janda atau Duda yang diterbitkan oleh Kelurahan.',
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
                    'status_perkawinan',
                    'pekerjaan',
                    'alamat_lengkap',
                    'keperluan',

                    // Data Orang yang Gaib dihapus sesuai permintaan

                    // Surat Pengantar RT/RW
                    'rt',
                    'rw',
                    'no_surat_pengantar',
                    'tanggal_surat_pengantar',

                    // Surat Pernyataan
                    'tanggal_surat_pernyataan',

                    // Lampiran SKJD
                    'skjd_surat_pengantar_rtrw',
                    'skjd_blangko_pernyataan',
                    'skjd_ktp_kk_bersangkutan',
                    'skjd_ktp_saksi',
                    'skjd_bukti_lunas_pbb',
                ],
                'is_active' => true,
            ]
        );

        $this->command->info("✓ JenisSurat SKJD (ID: {$skjd->id}) siap.");

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
                    'jenis_surat_id' => $skjd->id,
                    'kelurahan_id'   => $kelurahan->id,
                ],
                [
                    'nama'                       => 'Flow Kelurahan SKJD',
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

        $this->command->info('✓ SkjdSeeder selesai.');
    }
}
