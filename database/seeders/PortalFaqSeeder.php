<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PortalFaq;

class PortalFaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            // === UMUM ===
            [
                'pertanyaan' => 'Apa itu Kecamatan Landasan Ulin?',
                'jawaban'    => 'Kecamatan Landasan Ulin adalah salah satu kecamatan di Kota Banjarbaru, Kalimantan Selatan. Kecamatan ini melayani berbagai kebutuhan administrasi dan pelayanan publik bagi warga yang berdomisili di wilayahnya.',
                'kategori'   => 'umum',
                'urutan'     => 1,
                'status'     => 'aktif',
            ],
            [
                'pertanyaan' => 'Di mana lokasi kantor Kecamatan Landasan Ulin?',
                'jawaban'    => 'Kantor Kecamatan Landasan Ulin beralamat di Jl. A. Yani KM 20, Landasan Ulin, Kota Banjarbaru, Kalimantan Selatan. Anda juga dapat menemukan lokasi kami melalui fitur Peta Wilayah di portal ini.',
                'kategori'   => 'umum',
                'urutan'     => 2,
                'status'     => 'aktif',
            ],
            [
                'pertanyaan' => 'Berapa jam operasional pelayanan di kantor kecamatan?',
                'jawaban'    => 'Jam operasional pelayanan: Senin–Kamis pukul 08.00–16.00 WITA, Jumat pukul 08.00–11.30 WITA. Kantor tutup pada hari Sabtu, Minggu, dan hari libur nasional.',
                'kategori'   => 'umum',
                'urutan'     => 3,
                'status'     => 'aktif',
            ],
            [
                'pertanyaan' => 'Bagaimana cara menghubungi kantor kecamatan?',
                'jawaban'    => 'Anda dapat menghubungi kami melalui nomor telepon kantor atau mengunjungi langsung ke kantor kecamatan pada jam operasional. Informasi kontak lengkap tersedia di halaman Beranda portal ini.',
                'kategori'   => 'umum',
                'urutan'     => 4,
                'status'     => 'aktif',
            ],

            // === SURAT ===
            [
                'pertanyaan' => 'Apa saja jenis surat yang bisa diajukan secara online?',
                'jawaban'    => 'Melalui portal SiMPEL, warga dapat mengajukan berbagai jenis surat keterangan seperti Surat Keterangan Domisili, Surat Keterangan Tidak Mampu (SKTM), Surat Pengantar KTP/KK, dan berbagai surat keterangan lainnya sesuai kebutuhan.',
                'kategori'   => 'surat',
                'urutan'     => 1,
                'status'     => 'aktif',
            ],
            [
                'pertanyaan' => 'Berapa lama proses pengurusan surat?',
                'jawaban'    => 'Proses pengurusan surat secara online umumnya membutuhkan waktu 1–3 hari kerja tergantung jenis surat dan kelengkapan berkas yang diajukan. Anda dapat memantau status permohonan melalui fitur Cek Status di halaman utama.',
                'kategori'   => 'surat',
                'urutan'     => 2,
                'status'     => 'aktif',
            ],
            [
                'pertanyaan' => 'Dokumen apa saja yang diperlukan untuk mengajukan surat?',
                'jawaban'    => 'Persyaratan dokumen berbeda untuk tiap jenis surat. Secara umum, Anda perlu menyiapkan KTP, Kartu Keluarga (KK), dan dokumen pendukung sesuai jenis surat yang diajukan. Detail persyaratan akan ditampilkan saat memilih jenis surat di formulir pengajuan.',
                'kategori'   => 'surat',
                'urutan'     => 3,
                'status'     => 'aktif',
            ],
            [
                'pertanyaan' => 'Bagaimana cara mengecek status permohonan surat saya?',
                'jawaban'    => 'Anda dapat mengecek status permohonan melalui menu "Cek Status" di halaman utama portal. Masukkan nomor token permohonan yang diberikan saat pengajuan untuk melihat progres terkini.',
                'kategori'   => 'surat',
                'urutan'     => 4,
                'status'     => 'aktif',
            ],
            [
                'pertanyaan' => 'Apakah saya perlu datang ke kantor setelah mengajukan online?',
                'jawaban'    => 'Untuk beberapa jenis surat, pengambilan dokumen fisik tetap diperlukan di kantor kecamatan. Namun untuk surat yang dapat diterbitkan secara digital, Anda akan mendapatkan notifikasi dan dapat mengunduh langsung dari portal.',
                'kategori'   => 'surat',
                'urutan'     => 5,
                'status'     => 'aktif',
            ],

            // === KEPENDUDUKAN ===
            [
                'pertanyaan' => 'Bagaimana cara mengurus perubahan data KTP?',
                'jawaban'    => 'Perubahan data KTP dilayani di Dinas Kependudukan dan Pencatatan Sipil (Disdukcapil) Kota Banjarbaru. Pihak kecamatan dapat membuatkan surat pengantar yang diperlukan dalam proses tersebut.',
                'kategori'   => 'kependudukan',
                'urutan'     => 1,
                'status'     => 'aktif',
            ],
            [
                'pertanyaan' => 'Apakah kecamatan melayani pembuatan KTP dan KK?',
                'jawaban'    => 'Pembuatan KTP dan KK merupakan kewenangan Disdukcapil. Kecamatan berperan sebagai fasilitator dengan mengeluarkan surat pengantar yang diperlukan. Hubungi kantor kelurahan setempat untuk langkah awal.',
                'kategori'   => 'kependudukan',
                'urutan'     => 2,
                'status'     => 'aktif',
            ],

            // === PERIZINAN ===
            [
                'pertanyaan' => 'Apakah kecamatan menerbitkan izin usaha?',
                'jawaban'    => 'Kecamatan dapat menerbitkan surat keterangan domisili usaha sebagai salah satu syarat pengurusan izin usaha. Untuk perizinan usaha seperti NIB (Nomor Induk Berusaha), prosesnya dilakukan melalui sistem OSS (Online Single Submission) secara online.',
                'kategori'   => 'perizinan',
                'urutan'     => 1,
                'status'     => 'aktif',
            ],
        ];

        foreach ($faqs as $faq) {
            PortalFaq::create($faq);
        }

        $this->command->info('Seeder Portal FAQ berhasil: ' . count($faqs) . ' FAQ ditambahkan.');
    }
}
