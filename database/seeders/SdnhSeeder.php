<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisSurat;

class SdnhSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seed 'Surat Dispensasi Nikah' (SDNH) that bypasses kelurahan
     * and goes straight to admin_kecamatan.
     */
    public function run(): void
    {
        $sdnh = JenisSurat::updateOrCreate(
            ['kode' => 'SDNH'],
            [
                'nama'      => 'Surat Dispensasi Nikah',
                'deskripsi' => 'Surat Dispensasi Nikah yang memerlukan persetujuan langsung dari Kecamatan (tanpa melalui Lurah).',
                'template_path' => 'pdf.sdnh',
                // Biarkan array required_fields kosong KECUALI field yang sifatnya File/Attachment wajib diisi agar backend kita bisa membedakannya saat akan disimpan
                'required_fields' => [
                    ['name' => 'sdnh_surat_pengantar', 'label' => 'Surat Pengantar RT/RW', 'type' => 'file', 'is_required' => true],
                    ['name' => 'sdnh_ktp_kk', 'label' => 'KTP dan KK', 'type' => 'file', 'is_required' => true],
                    ['name' => 'sdnh_formulir_n', 'label' => 'Formulir N1-N5', 'type' => 'file', 'is_required' => true],
                    ['name' => 'sdnh_lunas_pbb', 'label' => 'Bukti Tanda Lunas PBB', 'type' => 'file', 'is_required' => true],
                    ['name' => 'sdnh_akta_cerai_mati', 'label' => 'Akta Cerai/Mati', 'type' => 'file', 'is_required' => false],
                ],
                'is_active' => true,
            ]
        );

        // Buat atau inisiasi Counter khusus Kecamatan (menggunakan default bulan 1 untuk reset tahunan)
        // Kita titipkan ke ID Kelurahan Landasan Ulin (6372010003) atau kelurahan pertama
        $kelurahanKecamatan = \App\Models\Kelurahan::where('kecamatan_id', '6372010')->first();
        if ($kelurahanKecamatan) {
            \App\Models\SuratCounter::firstOrCreate(
                [
                    'jenis_surat_id' => $sdnh->id,
                    'kelurahan_id'   => $kelurahanKecamatan->id,
                    'tahun'          => date('Y'),
                    'bulan'          => 1, // Fix bulan 1 agar resetnya per tahun, bukan per bulan
                ],
                ['counter' => 0]
            );
        }

        $this->command->info("✓ JenisSurat SDNH (ID: {$sdnh->id}) & Counter-nya siap.");
    }
}
