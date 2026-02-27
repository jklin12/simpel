<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PortalBerita;
use Carbon\Carbon;

class PortalBeritaSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            [
                'judul'        => 'Kecamatan Landasan Ulin Luncurkan Portal Layanan Digital SiMPEL',
                'konten'       => '<p>Kecamatan Landasan Ulin resmi meluncurkan Portal Layanan Digital yang diberi nama <strong>SiMPEL (Sistem Manajemen Pelayanan Elektronik)</strong>. Portal ini hadir sebagai wujud komitmen pemerintah kecamatan dalam meningkatkan kualitas pelayanan publik di era digital.</p>

<p>Dengan adanya SiMPEL, warga Kecamatan Landasan Ulin kini dapat mengajukan berbagai jenis surat keterangan secara daring tanpa harus antre di kantor kecamatan. Berbagai layanan tersedia mulai dari Surat Keterangan Domisili, Surat Keterangan Tidak Mampu (SKTM), hingga Surat Pengantar KTP dan Kartu Keluarga.</p>

<p>Camat Landasan Ulin menyampaikan bahwa peluncuran portal ini merupakan bagian dari program transformasi digital pemerintah daerah Kota Banjarbaru. "Kami berharap portal ini bisa membantu masyarakat dalam mengurus administrasi dengan lebih mudah, cepat, dan efisien," ujarnya dalam acara peluncuran.</p>

<p>Masyarakat dapat mengakses portal SiMPEL melalui perangkat apa pun yang terhubung dengan internet. Layanan pengajuan surat tersedia 24 jam sehari, 7 hari seminggu, sehingga warga tidak perlu khawatir soal jam operasional kantor.</p>

<p>Untuk informasi lebih lanjut mengenai cara penggunaan portal, warga dapat mengunjungi halaman FAQ atau menghubungi kantor kecamatan pada jam operasional.</p>',
                'status'       => 'published',
                'published_at' => Carbon::now()->subDays(2),
            ],
            [
                'judul'        => 'Pelaksanaan Musrenbang Kecamatan Landasan Ulin Tahun 2026',
                'konten'       => '<p>Kecamatan Landasan Ulin telah melaksanakan Musyawarah Rencana Pembangunan (Musrenbang) tingkat kecamatan untuk tahun anggaran 2026. Kegiatan ini berlangsung di Aula Kantor Kecamatan Landasan Ulin dan dihadiri oleh seluruh kepala kelurahan, tokoh masyarakat, serta perwakilan dari berbagai elemen warga.</p>

<p>Musrenbang merupakan forum perencanaan pembangunan yang mempertemukan pemerintah dengan masyarakat untuk membahas prioritas pembangunan di wilayah kecamatan. Berbagai usulan dari kelurahan-kelurahan di Kecamatan Landasan Ulin dibahas dan dirangkum dalam dokumen rencana kerja pembangunan daerah.</p>

<p>Beberapa usulan prioritas yang menjadi sorotan dalam musrenbang kali ini antara lain perbaikan infrastruktur jalan lingkungan, pembangunan fasilitas umum, peningkatan kualitas layanan kesehatan dan pendidikan, serta pengelolaan drainase untuk mengatasi masalah banjir di musim hujan.</p>

<p>Sekretaris Kecamatan Landasan Ulin menegaskan bahwa seluruh usulan yang masuk akan melalui proses seleksi dan prioritisasi berdasarkan kebutuhan mendesak masyarakat serta ketersediaan anggaran daerah. "Kami akan berupaya semaksimal mungkin agar setiap aspirasi masyarakat dapat terakomodir," tuturnya.</p>',
                'status'       => 'published',
                'published_at' => Carbon::now()->subDays(7),
            ],
            [
                'judul'        => 'Sosialisasi Program Bantuan Sosial untuk Warga Kurang Mampu',
                'konten'       => '<p>Kantor Kecamatan Landasan Ulin mengadakan kegiatan sosialisasi program bantuan sosial (bansos) yang ditujukan bagi warga kurang mampu di wilayah kecamatan. Kegiatan ini diselenggarakan bekerja sama dengan Dinas Sosial Kota Banjarbaru.</p>

<p>Dalam sosialisasi tersebut, petugas menjelaskan berbagai program bantuan yang tersedia, antara lain Program Keluarga Harapan (PKH), Bantuan Pangan Non-Tunai (BPNT), serta bantuan langsung tunai (BLT) untuk keluarga miskin. Warga juga mendapat penjelasan mengenai mekanisme pendaftaran dan persyaratan yang diperlukan.</p>

<p>Petugas dari Dinas Sosial turut hadir untuk memberikan informasi mengenai Data Terpadu Kesejahteraan Sosial (DTKS) dan menjelaskan cara masyarakat yang belum terdaftar dapat mengajukan permohonan. Bagi warga yang memerlukan Surat Keterangan Tidak Mampu (SKTM) sebagai salah satu persyaratan, kini dapat mengurusnya secara online melalui portal SiMPEL.</p>

<p>Kegiatan sosialisasi ini disambut antusias oleh warga. Banyak pertanyaan yang masuk, terutama seputar cara memastikan nama mereka terdaftar dalam DTKS dan langkah yang harus dilakukan jika tidak terdaftar.</p>',
                'status'       => 'published',
                'published_at' => Carbon::now()->subDays(14),
            ],
            [
                'judul'        => 'Program Vaksinasi Massal Tingkat Kecamatan Berjalan Lancar',
                'konten'       => '<p>Program vaksinasi massal yang diselenggarakan di tingkat Kecamatan Landasan Ulin berjalan dengan lancar dan mendapat respons positif dari masyarakat. Kegiatan ini merupakan bagian dari upaya peningkatan kesehatan masyarakat yang diprogramkan oleh Dinas Kesehatan Kota Banjarbaru.</p>

<p>Vaksinasi dilaksanakan di beberapa titik yang tersebar di seluruh kelurahan yang ada di Kecamatan Landasan Ulin, sehingga memudahkan akses bagi seluruh lapisan masyarakat. Tenaga kesehatan dari puskesmas setempat dikerahkan untuk memastikan kelancaran proses vaksinasi.</p>

<p>Camat Landasan Ulin mengapresiasi tingginya antusiasme warga dalam mengikuti program vaksinasi ini. Beliau berharap seluruh warga kecamatan dapat tervaksinasi sehingga tercipta kekebalan kelompok (herd immunity) yang melindungi seluruh masyarakat.</p>

<p>Bagi warga yang belum sempat mengikuti vaksinasi, informasi mengenai jadwal dan lokasi vaksinasi berikutnya dapat diperoleh melalui kantor kelurahan setempat atau melalui pengumuman resmi di portal kecamatan ini.</p>',
                'status'       => 'published',
                'published_at' => Carbon::now()->subDays(21),
            ],
            [
                'judul'        => 'Kerja Bakti Massal Bersihkan Lingkungan Menyambut HUT Kota Banjarbaru',
                'konten'       => '<p>Dalam rangka menyambut Hari Ulang Tahun Kota Banjarbaru, seluruh warga Kecamatan Landasan Ulin bersama aparat kecamatan dan kelurahan menggelar kegiatan kerja bakti massal. Kegiatan ini bertujuan untuk membersihkan lingkungan dan mempererat tali silaturahmi antar warga.</p>

<p>Kerja bakti dilakukan secara serentak di seluruh RT dan RW yang ada di Kecamatan Landasan Ulin. Warga bergotong royong membersihkan selokan, memangkas rumput liar, mengecat pagar, serta mendirikan umbul-umbul dan dekorasi untuk memeriahkan suasana HUT kota.</p>

<p>"Kegiatan ini bukan sekadar membersihkan lingkungan, tapi juga menjadi momentum untuk mempererat hubungan antar sesama warga," kata salah satu Ketua RW yang ikut berpartisipasi. Semangat gotong royong ini diharapkan terus terjaga dan menjadi bagian dari budaya masyarakat Landasan Ulin.</p>

<p>Pihak kecamatan juga memanfaatkan momen kerja bakti ini untuk menyosialisasikan program-program pemerintah kepada warga, termasuk menginformasikan keberadaan portal layanan digital SiMPEL yang dapat dimanfaatkan untuk mengurus berbagai keperluan administrasi.</p>',
                'status'       => 'published',
                'published_at' => Carbon::now()->subDays(30),
            ],
            [
                'judul'        => 'Pelatihan UMKM: Kecamatan Dorong Pertumbuhan Ekonomi Lokal',
                'konten'       => '<p>Kecamatan Landasan Ulin menyelenggarakan pelatihan bagi para pelaku Usaha Mikro, Kecil, dan Menengah (UMKM) di wilayahnya. Pelatihan ini bertujuan untuk meningkatkan kapasitas dan daya saing pelaku usaha lokal di era persaingan digital yang semakin ketat.</p>

<p>Berbagai materi disajikan dalam pelatihan ini, mulai dari manajemen keuangan sederhana, strategi pemasaran digital, cara mendaftarkan usaha dan mendapatkan Nomor Induk Berusaha (NIB) melalui sistem OSS, hingga pemanfaatan platform e-commerce untuk memperluas jangkauan pasar.</p>

<p>Peserta pelatihan yang berjumlah sekitar 50 pelaku UMKM tampak antusias mengikuti setiap sesi. Mereka berharap ilmu yang didapat dapat langsung diterapkan untuk mengembangkan usaha mereka. "Selama ini kami hanya jualan di pasar tradisional, sekarang kami ingin coba jualan online juga," ujar salah satu peserta.</p>

<p>Kecamatan Landasan Ulin berkomitmen untuk terus mendukung pertumbuhan UMKM lokal melalui berbagai program pemberdayaan ekonomi masyarakat. Program serupa akan terus diadakan secara berkala untuk memastikan seluruh pelaku usaha di wilayah kecamatan mendapatkan kesempatan yang sama untuk berkembang.</p>',
                'status'       => 'published',
                'published_at' => Carbon::now()->subDays(45),
            ],
            [
                'judul'        => 'Pengumuman: Libur Nasional dan Jadwal Pelayanan Kecamatan',
                'konten'       => '<p>Sehubungan dengan adanya hari libur nasional dalam waktu dekat, Kantor Kecamatan Landasan Ulin menyampaikan informasi mengenai penyesuaian jadwal pelayanan kepada masyarakat.</p>

<p>Selama hari libur nasional, kantor kecamatan akan tutup dan tidak ada pelayanan tatap muka. Namun, masyarakat tetap dapat mengajukan permohonan surat secara online melalui portal SiMPEL. Pengajuan yang masuk selama masa libur akan diproses pada hari kerja berikutnya.</p>

<p>Pelayanan normal akan kembali beroperasi setelah masa libur berakhir, yakni pada hari kerja pertama setelah tanggal libur. Jam pelayanan tetap seperti biasa: Senin-Kamis pukul 08.00-16.00 WITA, dan Jumat pukul 08.00-11.30 WITA.</p>

<p>Untuk keperluan mendesak yang tidak dapat ditunda, masyarakat dapat menghubungi kantor kecamatan melalui nomor yang tercantum di halaman Beranda portal ini. Kami mohon pengertian seluruh masyarakat atas ketidaknyamanan yang mungkin terjadi.</p>',
                'status'       => 'published',
                'published_at' => Carbon::now()->subDays(5),
            ],
            [
                'judul'        => 'Peningkatan Infrastruktur Jalan: Proyek Pengaspalan Dimulai',
                'konten'       => '<p>Pemerintah Kota Banjarbaru melalui dinas terkait telah memulai proyek peningkatan infrastruktur jalan di beberapa titik di Kecamatan Landasan Ulin. Proyek ini merupakan tindak lanjut dari usulan warga yang disampaikan dalam Musrenbang tingkat kecamatan.</p>

<p>Pengerjaan meliputi perbaikan dan pengaspalan jalan lingkungan yang selama ini mengalami kerusakan akibat usia dan beban kendaraan. Beberapa ruas jalan yang menjadi prioritas adalah jalan-jalan yang menghubungkan pemukiman warga dengan fasilitas umum seperti sekolah, puskesmas, dan pasar.</p>

<p>Warga diminta untuk bersabar dan memaklumi adanya gangguan lalu lintas selama proses pengerjaan berlangsung. Pengerjaan diperkirakan akan selesai dalam beberapa minggu ke depan, tergantung kondisi cuaca dan kelancaran pasokan material.</p>

<p>Camat Landasan Ulin berharap agar proyek ini dapat meningkatkan kenyamanan dan keselamatan warga dalam berkendara. "Kami terus berupaya untuk meningkatkan kualitas infrastruktur demi kesejahteraan masyarakat," tegasnya.</p>',
                'status'       => 'published',
                'published_at' => Carbon::now()->subDays(10),
            ],
            [
                'judul'        => 'Rapat Koordinasi Ketua RT/RW Se-Kecamatan Landasan Ulin',
                'konten'       => '<p>Kecamatan Landasan Ulin mengadakan rapat koordinasi yang dihadiri oleh seluruh Ketua RT dan RW se-kecamatan. Rekap ini dilaksanakan secara rutin setiap triwulan sebagai forum komunikasi antara aparatur kecamatan dengan pengurus lingkungan di tingkat bawah.</p>

<p>Dalam rapat kali ini, beberapa agenda utama dibahas antara lain: evaluasi program-program yang sedang berjalan di masing-masing kelurahan, koordinasi terkait pendataan warga untuk program bantuan sosial, serta sosialisasi kebijakan-kebijakan terbaru dari pemerintah kota yang perlu disebarluaskan ke tingkat RT dan RW.</p>

<p>Selain itu, para Ketua RT dan RW juga diberi pembekalan mengenai penggunaan portal digital SiMPEL. Mereka diharapkan dapat menjadi ujung tombak dalam mensosialisasikan layanan digital ini kepada warga yang masih belum familiar dengan teknologi.</p>

<p>Rapat berjalan produktif dengan banyaknya masukan dan aspirasi yang disampaikan oleh para Ketua RT dan RW. Semua aspirasi tersebut akan dicatat dan ditindaklanjuti sesuai dengan kewenangan yang ada.</p>',
                'status'       => 'published',
                'published_at' => Carbon::now()->subDays(18),
            ],
            [
                'judul'        => 'Draft: Pengumuman Penerimaan Beasiswa Pendidikan (Segera Terbit)',
                'konten'       => '<p>Pemerintah Kota Banjarbaru melalui Dinas Pendidikan akan segera membuka pendaftaran beasiswa untuk pelajar berprestasi dan kurang mampu yang berdomisili di Kota Banjarbaru, termasuk Kecamatan Landasan Ulin.</p>

<p>Program beasiswa ini terbuka untuk jenjang SMP, SMA/SMK, hingga perguruan tinggi. Calon penerima beasiswa wajib memenuhi sejumlah persyaratan yang akan diumumkan secara resmi dalam waktu dekat.</p>

<p>Salah satu dokumen yang kemungkinan diperlukan adalah Surat Keterangan Tidak Mampu (SKTM) dari kecamatan. Warga dapat mengurus SKTM secara online melalui portal SiMPEL tanpa harus datang ke kantor.</p>

<p>Pantau terus portal ini dan pengumuman resmi dari Dinas Pendidikan Kota Banjarbaru untuk informasi lebih lanjut mengenai jadwal dan mekanisme pendaftaran beasiswa.</p>',
                'status'       => 'draft',
                'published_at' => null,
            ],
        ];

        foreach ($articles as $article) {
            $article['slug'] = PortalBerita::generateSlug($article['judul']);
            PortalBerita::create($article);
        }

        $this->command->info('Seeder Portal Berita berhasil: ' . count($articles) . ' berita ditambahkan (9 published, 1 draft).');
    }
}
