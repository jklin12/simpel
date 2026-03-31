# Release Notes

## [v1.0.4] - 2026-03-31

### Added — Integrasi Jenis Surat Baru (SKJD, SKSI, SKG)

- **Surat Keterangan Janda/Duda (SKJD)**:
  - Form pengajuan dan template cetak PDF untuk pengurusan administrasi terkait status Janda/Duda.
  - Implementasi nomor surat otomatis format `400.12.3.3/...-SMPL/...`.
  - Telah dihapusnya field "Data Orang Gaib" pada tahap pengajuan SKJD untuk menyelaraskan dengan persyaratan yang relevan.
- **Surat Keterangan Suami Istri (SKSI)**:
  - Form pengajuan terintegrasi sinkron antara Data Diri Bersangkutan dan Data Istri/Pasangan.
  - Dilengkapi antarmuka pemindaian mutakhir **OCR KTP** otomatis yang juga bisa dioperasikan untuk ekstraksi data Pasangan.
  - Input "Pekerjaan" pada data pasangan kini menggunakan elemen penelusuran *dropdown* canggih (**TomSelect**) yang responsif.
  - Implementasi urutan nomor regulasi surat bermatra `400.12.3.4/...-SMPL/...`.
- **Surat Keterangan Gaib (SKG)**:
  - Ekstensi form permohonan khusus bagi pelaporan masyarakat atas subjek anggota keluarga yang hilang (gaib).
  - Mekanisme **OCR KTP** auto-fill dan *dropdown dropdown* canggih terpasang memfasilitasi "Data Orang Gaib", sama interaktifnya dengan antarmuka yang lain.
  - Menetapkan nomor administrasi berformat `400.12.3.5/...-SMPL/...`.

### Instruksi Deployment (Seeder)
Agar semua format dokumen, requirement form, dan alur _approval_ dari tiga tipe registrasi *Surat* interaktif ini tereksekusi pada level _database server_, wajib menjalankan perintah seeder *database* secara manual (atau via server deployment logic) sebagai berikut:

```bash
php artisan db:seed --class=SkjdSeeder
php artisan db:seed --class=SksiSeeder
php artisan db:seed --class=SkgSeeder
```## [v1.0.3] - 2026-03-09

### Added — Template Surat Keterangan Menikah (SKMH)

- **Jenis Surat Baru (SKMH)**: Ditambahkan tipe surat baru **Surat Keterangan Menikah (SKMH)** lengkap dengan seeder (`SkmhSeeder`), alur persetujuan per kelurahan, validasi form, dan template cetak PDF.
- **Form Pengajuan Custom `types/skmh.blade.php`**: Form pengajuan SKMH terdiri dari 4 bagian:
  - **Data Diri Pemohon** — dengan fitur *Scan KTP (OCR)* dan dropdown Status Perkawinan khusus nikah (Jejaka / Duda / Beristri ke-X / Perawan / Janda).
  - **Data Orang Tua — Ayah** — Nama, Bin, NIK, Agama, Kewarganegaraan, TTL, Pekerjaan, Alamat.
  - **Data Orang Tua — Ibu** — Nama, Binti, NIK, Agama, Kewarganegaraan, TTL, Pekerjaan, Alamat.
  - **Upload Berkas** — 7 lampiran wajib + 5 lampiran opsional (Akta Cerai, Dispensasi Kawin, Izin TNI/POLRI, Izin Poligami, Rekom DP3A).
- **Template PDF 2 Halaman `pdf/skmh.blade.php`**:
  - **Halaman 1 (Cover)**: Surat Keterangan Untuk Nikah dengan nama, BIN/BINTI, alamat pemohon, dekorasi cincin nikah SVG, dan logo SiMPEL.
  - **Halaman 2 (Formulir Pengantar Nikah — Blanko N1)**: Data pemohon dalam format daftar bernomor (1–9), Status Perkawinan terpisah a/b (Laki-laki / Perempuan), data Ayah ("Adalah benar anak dari perkawinan seorang pria:"), data Ibu ("Dengan seorang wanita:"), tanda tangan Lurah + QR code.
- **Nomor Surat SKMH**: Format baru `472-SMPL/XXX/RomawiiBulan/KodeKelurahan/Tahun`.

### Changed
- **Format Nomor Surat semua tipe**: Semua kode surat kini ditambahkan suffix `-SMPL` (contoh: `400.12.3.1-SMPL/...`, `600.2-SMPL/...`, `500-SMPL/...`, `400.12-SMPL/...`) untuk membedakan surat yang diterbitkan melalui sistem SiMPEL.
- **Validasi Request SKMH**: Menambahkan `getSkmhRules()` pada `StorePermohonanRequest` dengan aturan per-field untuk data pemohon, data orang tua, dan semua lampiran (wajib dan opsional).

## [v1.0.2] - 2026-03-09

### Added / Improvements
- **Notifikasi WhatsApp Multi-Role**: Sistem kini dapat mengirimkan notifikasi permohonan baru (`PermohonanCreatedWhatsapp`) dan revisi (`PermohonanRevisiNotification`) tidak hanya ke pemohon, tetapi juga kepada `admin_kelurahan`, `admin_kecamatan`, dan `super_admin` untuk meningkatkan *awareness*.
- **Pesan WhatsApp Berbasis Role**: Pesan WhatsApp kini disesuaikan otomatis berdasarkan penerima. Pemohon mendapatkan pesan berisi arahan dan progres, sedangkan admin menerima pesan ajakan untuk memverifikasi atau menandatangani dokumen.
- **Attachment Draft PDF otomatis via WhatsApp**: Menambahkan fungsionalitas pengiriman dokumen draf persetujuan dalam bentuk PDF yang dilampirkan langsung via WhatsApp ke akun `admin_kelurahan` (Lurah) pada saat persetujuan permohonan, mempermudah dan mempercepat proses penandatanganan surat.
- **Konfigurasi API WhatsApp Dinamis**: Mengubah *hardcoded url* sehingga konfigurasi Basic Auth (`username`, `password`) dan Base URL penyedia WhatsApp gateway diletakkan tersentralisasi di `config/services.php` dan file `.env`.
- **Pengalihan WA untuk Environment Local**: Menambahkan fitur _intercept_ pada `WhatsAppChannel` yang akan mengalihkan semua notifikasi WA ke satu nomor testing khusus secara statis ketika environment server adalah `local`.

## [v1.0.1] - 2026-03-04

### Added / Added Features
- **Manajemen Sub Kategori Data Kelurahan**: Menambahkan fitur _dynamic dependent dropdown_ untuk sub kategori Fasilitas (seperti Tempat Ibadah, Pemakaman, Pendidikan, dan fasilitas Umum lainnya) dan input kelengkapan _Status Instansi_ (Negeri/Swasta).
- **Pemetaan Wilayah (RT/RW) pada Data Kelurahan**: Data Kelurahan kini dilengkapi dengan pencatatan unit geografis spesifik `rt` dan `rw`. Kolom filter pencarian Peta/Tabel Admin untuk `rt` dan `rw` juga telah ditambahkan.
- **Peta Interaktif Layanan Publik (Frontend)**: 
  - Marker detail di Peta sekarang menampilkan nama jenis fasilitas selengkapnya.
  - Implementasi *Live-Search Filtering*: Peta di beranda portal kini bisa melakukan pencarian dinamis (berdasarkan nama lokasi, kategori fasilitas, alamat, maupun letak spesifik lingkungan RT/RW).
  - Integrasi ikon Peta secara dinamis (khusus untuk *Tempat Ibadah* ikon menyesuaikan dengan agama tempat ibadahnya secara otomatis).

### Changed / Improvements
- **Perbaikan Roles & Access Control Kelurahan**: Role `admin_kelurahan` telah ditambahkan otomatisasi filtering, yaitu mereka hanya dapat menambah, memodifikasi, dan membaca entitas data milik Kelurahan mereka masing-masing saja. Input pilihan `kelurahan_id` pada saat manajemen data telah disembunyikan/di-hold otomatis ke _identifier_ akun tersebut.
- **Pembaruan UI Peta Beranda**: Mengubah durasi _carousel/slider_ berita menjadi 10 detik dan mematikan visibilitas pointer slider saat laman dibuka di tampilan _mobile_ guna menyediakan *Layout* peta yang diutamakan.
- **Skema Database**: Merombak kolom `kategori` tabel Data Kelurahan (`portal_data_kelurahans`) yang semula bertipe data `enum` menjadi `varchar(255)` untuk memberikan skalabilitas kategori *Fasilitas Umum* dsb di masa lalu dan depannya.

---

*This release note maintains tracking over incremental administrative adjustments bridging dynamic filtering maps and role scoping permissions.*
