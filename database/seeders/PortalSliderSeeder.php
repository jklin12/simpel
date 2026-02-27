<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PortalSlider;

class PortalSliderSeeder extends Seeder
{
    public function run(): void
    {
        $sliders = [
            [
                'judul'       => 'Informasi & Layanan Kecamatan Landasan Ulin',
                'sub_judul'   => 'Akses berita terbaru, peta fasilitas, dan layanan administrasi surat menyurat secara online.',
                'gambar'      => null, // isi oleh admin via halaman manajemen
                'warna_tema'  => 'blue',
                'label_cta_1' => 'Baca Berita',
                'url_cta_1'   => '/berita',
                'label_cta_2' => 'Ajukan Surat',
                'url_cta_2'   => '/layanan',
                'urutan'      => 1,
                'status'      => 'aktif',
            ],
            [
                'judul'       => 'Dekat dengan Masyarakat, Mudah Diakses',
                'sub_judul'   => 'Layanan cepat untuk warga—pengajuan surat, cek status, dan informasi wilayah.',
                'gambar'      => null,
                'warna_tema'  => 'green',
                'label_cta_1' => 'Cek Status Surat',
                'url_cta_1'   => '/tracking',
                'label_cta_2' => 'Lihat Peta',
                'url_cta_2'   => '/peta',
                'urutan'      => 2,
                'status'      => 'aktif',
            ],
            [
                'judul'       => 'Temukan Lokasi Penting di Kecamatan',
                'sub_judul'   => 'Lokasi Ketua RT, RW, fasilitas umum, dan tempat ibadah tersedia di peta interaktif kami.',
                'gambar'      => null,
                'warna_tema'  => 'orange',
                'label_cta_1' => 'Buka Peta Wilayah',
                'url_cta_1'   => '/peta',
                'label_cta_2' => null,
                'url_cta_2'   => null,
                'urutan'      => 3,
                'status'      => 'aktif',
            ],
        ];

        foreach ($sliders as $slider) {
            PortalSlider::create($slider);
        }

        $this->command->info('Seeder Portal Slider berhasil dijalankan!');
    }
}
