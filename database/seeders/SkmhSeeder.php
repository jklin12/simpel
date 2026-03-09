<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisSurat;
use App\Models\ApprovalFlow;
use App\Models\Kelurahan;

class SkmhSeeder extends Seeder
{
    /**
     * Seed jenis surat SKMH + approval flow untuk semua kelurahan
     * di kecamatan Landasan Ulin (kecamatan_id 6372010).
     */
    public function run(): void
    {
        // ---- 1. JenisSurat ----
        $skmh = JenisSurat::updateOrCreate(
            ['kode' => 'SKMH'],
            [
                'nama'      => 'Surat Keterangan Menikah',
                'deskripsi' => 'Surat Pengantar / Keterangan untuk mengurus pernikahan yang diterbitkan oleh Kelurahan.',
                'template_path' => null, // akan dihandle secara khusus dengan blade pdf
                'required_fields' => [
                    // Data Pemohon
                    'nama_lengkap',
                    'nik_bersangkutan',
                    'jenis_kelamin',
                    'agama',
                    'kewarganegaraan',
                    'tempat_lahir',
                    'tanggal_lahir',
                    'pekerjaan',
                    'alamat_lengkap',
                    'status_perkawinan',

                    // Data Ayah
                    'ayah_nama',
                    'ayah_bin',
                    'ayah_nik',
                    'ayah_agama',
                    'ayah_kewarganegaraan',
                    'ayah_tempat_lahir',
                    'ayah_tanggal_lahir',
                    'ayah_pekerjaan',
                    'ayah_alamat',

                    // Data Ibu
                    'ibu_nama',
                    'ibu_binti',
                    'ibu_nik',
                    'ibu_agama',
                    'ibu_kewarganegaraan',
                    'ibu_tempat_lahir',
                    'ibu_tanggal_lahir',
                    'ibu_pekerjaan',
                    'ibu_alamat',

                    // Lampiran SKMH
                    'skmh_surat_pengantar',
                    'skmh_akta_ijazah_catin',
                    'skmh_ktp_kk_catin',
                    'skmh_ktp_kk_ortu',
                    'skmh_pas_foto',
                    'skmh_ktp_saksi',
                    'skmh_form_n2_n5',
                    'skmh_akta_cerai_kematian',
                    'skmh_dispensasi_pengadilan',
                    'skmh_izin_atasan',
                    'skmh_izin_poligami',
                    'skmh_rekom_dp3a',
                    'skmh_bukti_pbb',
                ],
                'is_active' => true,
            ]
        );

        $this->command->info("✓ JenisSurat SKMH (ID: {$skmh->id}) siap.");

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
                    'jenis_surat_id' => $skmh->id,
                    'kelurahan_id'   => $kelurahan->id,
                ],
                [
                    'nama'                       => 'Flow Kelurahan SKMH',
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

        $this->command->info('✓ SkmhSeeder selesai.');
    }
}
