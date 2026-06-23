# Release Notes

## [v1.0.7] - 2026-06-23

### Added — Google Analytics (Firebase/GA4) Integration

- **Automatic Pageview Tracking**: Implemented gtag.js script for automatic tracking of all page visits across public portal and admin dashboard.
- **Custom Events Tracking** (Privacy-Safe, Public Portal Only):
  - **`permohonan_submitted`** — Tracks form submission with token reference for deduplication
  - **`tracking_status_viewed`** — Tracks status check requests with jenis_surat and status parameters
  - **`surat_downloaded`** — Tracks letter download with jenis_surat parameter
- **Configuration**:
  - Added `firebase` config block to `config/services.php` reading from `.env`
  - Added `FIREBASE_MEASUREMENT_ID` placeholder to `.env.example`
  - All tracking conditional — only loads if Measurement ID is set (safe for local/testing environments)
- **Implementation Details**:
  - `resources/views/layouts/landing.blade.php` — gtag.js script injection for public portal
  - `resources/views/layouts/app.blade.php` — gtag.js script injection for admin dashboard
  - `resources/views/services/index.blade.php` — Success modal with `permohonan_submitted` event and analytics push
  - `resources/views/user/permohonan/track.blade.php` — `tracking_status_viewed` and `surat_downloaded` events with jenis_surat parameter
- **Documentation**: 
  - `FIREBASE_SETUP.md` — Comprehensive setup guide including:
    - Step-by-step Firebase project creation
    - Development, staging, and production environment configuration
    - Real-time testing and verification procedures
    - Troubleshooting guide for common issues
    - Firebase console navigation for metrics analysis
    - Production deployment checklist

### Security & Privacy

- **No Personal Data Sent**: All events exclude NIK, phone numbers, names, addresses, and track_tokens
- **Safe Event Parameters**: Events only send aggregate data (letter type, status, timestamps)
- **Conditional Loading**: gtag.js and measurement ID checks prevent errors in environments without Firebase configured

### Deployment Instructions

```bash
# 1. Create Firebase project and get Measurement ID (G-XXXXXXXXXX format)
# 2. Add to .env:
FIREBASE_MEASUREMENT_ID=G-XXXXXXXXXX

# 3. Clear config cache
php artisan config:clear

# 4. Test locally:
php artisan serve
# Open DevTools → Network tab → verify googletagmanager.com loads
```

### Notes

- Admin panel includes automatic pageview tracking; no custom events added (can be extended in future)
- Events are privacy-compliant per GDPR/local regulations — only aggregate metrics
- Measurement ID can be same for dev/prod or separate (separate projects recommended for production)
- See `FIREBASE_SETUP.md` for detailed Firebase connection instructions and multi-environment setup

---

## [v1.0.6] - 2026-05-10

### Added — Jenis Surat Baru (Tingkat Kecamatan & Kelurahan)

- **SPKDK — Surat Pengantar Keterangan Domisili Kepartaian** *(Tingkat Kelurahan, TTD Lurah)*:
  - Form publik statik 4 seksi: Data Pemohon (OCR KTP), Data Kantor/Partai, Surat Pengantar RT/RW, Upload Berkas.
  - 4 file upload wajib: Surat Pengantar RT/RW, KTP & KK Pemohon, Struktur Organisasi/SK Kepengurusan, Bukti Lunas PBB-P2.
  - Nomor surat format `200.1.5/{counter:03d}/{romanBulan}/KEL.{akronim}/{tahun}` — contoh: `200.1.5/001/VI/KEL.LU/2026`.
  - Approval flow: hanya `admin_kelurahan` (tidak diteruskan ke kecamatan).
  - Template PDF kop kelurahan, narasi berdasarkan surat pengantar RT/RW, TTD Lurah + QR code.

- **SKDK — Surat Pengantar Domisili Kepartaian** *(Tingkat Kecamatan, TTD Camat)*:
  - Form publik statik dengan OCR KTP, data organisasi/partai, data bangunan kantor, surat pengantar kelurahan.
  - 7 file upload wajib: Surat Pengantar RT/RW, KTP Pemohon, Akta Pendirian/Kepengurusan, Akta Pendirian Partai, IMB/Perjanjian Sewa, Surat Pengantar Lurah, Bukti Lunas PBB-P2.
  - Nomor surat format `200.1.5/{counter:03d}/{romanBulan}/kec.{akronim}/{tahun}`.
  - Approval flow: `admin_kelurahan` → `admin_kecamatan` (2 tahap, Camat yang TTD).
  - Template PDF kop kecamatan, TTD Camat + QR code.

- **ROIPK — Rekomendasi Operasional Izin Penyelenggaraan Kursus** *(Tingkat Kecamatan, TTD Camat)*:
  - Form publik statik dengan OCR KTP, data lembaga kursus, data bangunan, surat pengantar kelurahan.
  - 10 file upload (9 wajib + 1 opsional): Surat Pengantar RT/RW, KTP & KK Pemohon, Surat Permohonan, Struktur Organisasi, Ijazah Kompetensi, Izin Tetangga, Daftar Fasilitas, Silabus, Surat Pengantar Lurah; Bukti Lunas PBB-P2 (opsional).
  - Nomor surat format `{counter:03d}/ROIPK/kec.{akronim}/{romanBulan}/{tahun}`.
  - Approval flow: `admin_kelurahan` → `admin_kecamatan` (2 tahap, Camat yang TTD).
  - Template PDF kop kecamatan, TTD Camat + QR code.

### Instruksi Deployment — Jenis Surat Baru

```bash
# Daftarkan SKDK, ROIPK, dan SPKDK ke database
php artisan db:seed --class=JenisSuratSeeder
```

---

### Added — Alur Permintaan Revisi Surat (Revision Request Workflow)

- **Model PermohonanRevisiRequest**: Model baru untuk mengelola permintaan revisi pada surat yang sudah disetujui (`approved` status).
- **Workflow Revisi Lengkap**: Admin kelurahan/kecamatan dapat mengajukan request perubahan surat yang sudah approved, dengan approval flow terstruktur:
  - `admin_kelurahan` atau `admin_kecamatan` mengajukan request perubahan via `requestPerubahan()`
  - Request masuk ke tahap review dengan status `revision_requested` 
  - `admin_kecamatan` dan `super_admin` dapat **approve** atau **reject** revision request
  - Jika approve: surat kembali ke status `approved` dengan `revision_count` bertambah
  - Jika reject: surat tetap `approved` dengan catatan reject di notification
- **Notifikasi Multi-Channel (Database + WhatsApp)**: 
  - `RevisiRequestedNotification` & `RevisiRequestedWhatsapp` — dikirim ke admin_kecamatan/super_admin saat ada request perubahan
  - `RevisiRequestApprovedNotification` & `RevisiRequestApprovedWhatsapp` — notifikasi approval revisi
  - `RevisiRequestRejectedNotification` & `RevisiRequestRejectedWhatsapp` — notifikasi rejection revisi
- **UI Admin untuk Revision Requests**: 
  - Halaman show permohonan menampilkan daftar revision requests dengan status timeline
  - Button actions untuk approve/reject revision requests dengan modal form (optional catatan)
  - Visual indicators untuk revisi yang masuk dan status perubahannya

### Fixed — WhatsApp Notification Tidak Terproses pada requestPerubahan

- **Silent Failure pada WhatsAppChannel**: Masalah dimana notifikasi WhatsApp tidak terkirim tanpa log/error ketika recipient (User) tidak memiliki phone number.
  - **Root Cause**: Admin users di-create tanpa phone number di seeder, dan WhatsAppChannel return silent jika no phone found
  - **Fix#1**: Tambah warning log di WhatsAppChannel ketika notifikasi skip karena no phone number
  - **Fix#2**: Update `UserRoleSeeder` untuk set phone numbers ke semua admin users:
    - `super_admin`: +6281234567890
    - `admin_kecamatan`: +6282234567890
    - `admin_kelurahan`: +6283234567890
  - **Fix#3**: Migration untuk update existing admin users dengan phone numbers
  - **Fix#4**: Enhanced logging di `requestPerubahan()` untuk track recipient email & phone sebelum & sesudah notifikasi terkirim

### Improved — Logging & Observability

- Detailed logging di `PermohonanSuratService::requestPerubahan()` dengan recipient phone number tracking
- Warning log di `WhatsAppChannel::send()` untuk missing phone number scenarios
- Better error context untuk notification failures di revision request workflow

### Database Schema Changes

```bash
# Migrations yang ditambahkan:
- 2026_05_10_000001_add_revision_statuses_to_permohonan_surats.php
  → Tambah columns: revision_count (int), revision_requested_at (timestamp)
  
- 2026_05_10_000002_create_permohonan_revisi_requests_table.php
  → Buat table permohonan_revisi_requests dengan fields:
    - permohonan_surat_id
    - requested_by_user_id
    - approved_by_user_id (nullable)
    - alasan (text)
    - status (enum: pending, approved, rejected)
    - revision_number
    - approved_at, rejected_at (timestamps nullable)

- 2026_05_10_223453_update_admin_users_with_phone_numbers.php
  → Update admin users (super_admin, admin_kecamatan, admin_kelurahan) dengan phone numbers
```

### Instruksi Deployment

```bash
# 1. Run migrations untuk create/update tables dan admin phone numbers
php artisan migrate

# 2. (Jika re-seed diperlukan - hanya jika database baru)
php artisan db:seed --class=UserRoleSeeder

# 3. Restart queue worker untuk menjalankan notification jobs
sudo supervisorctl restart simpel-queue:*
```

### Notes untuk Production

- Pastikan semua admin users memiliki phone number yang valid sebelum menggunakan revision request feature
- WhatsApp gateway harus ter-konfigurasi dengan benar di `.env`:
  ```env
  WHATSAPP_BASE_URL=http://127.0.0.1:5003
  WHATSAPP_USERNAME=...
  WHATSAPP_PASSWORD=...
  WHATSAPP_TEST_NUMBER=  # Untuk env local, optional
  ```
- Monitor `whatsapp_notification_logs` table untuk tracking pengiriman notifikasi revisi

---

## [v1.0.5] - 2026-04-06

### Added — Fitur Hapus Permohonan & Soft Delete
- **Penghapusan Aman**: Admin Kelurahan dan Kecamatan kini dapat menghapus permohonan yang masih berstatus `pending`.
- **Mekanisme Soft Delete**: Menghapus data permohonan secara logis tanpa menghilangkan record fisik, menjaga integritas audit trail untuk dokumen dan approval terkait.
- **Izin Keamanan (Permission)**: Penambahan izin `delete_permohonan` yang diberikan otomatis kepada `super_admin`, `admin_kecamatan`, dan `admin_kelurahan`.

### Improved — Modernisasi Dashboard Permohonan
- **Redesain Antarmuka Indeks**: Transformasi visual daftar permohonan dengan tata letak yang lebih bersih, spasi yang lega, dan kartu-kartu informasi yang responsif.
- **Efisiensi Data**: Penggunaan baris ganda (Two-line display) untuk menampilkan Nomor Permohonan, Nomor Surat, data Pemohon, dan Waktu pengajuan secara padat informasi.
* **Status Badges Interaktif**: Lencana status baru dengan indikator visual dinamis (contoh: *pulse* pada status pending) untuk kemudahan pemantauan.

### Added — Master Data Kelurahan
- **Quick Status Toggle**: Penambahan fitur pembaruan status aktif/non-aktif Kelurahan secara langsung melalui daftar Master Data tanpa harus masuk ke halaman edit.

### Fixed — Validasi NIK & Duplikasi
- **Verifikasi Harian**: Implementasi pencegahan pengajuan ganda untuk NIK yang sama pada jenis surat yang sama dalam satu hari kalender guna mengurangi redundansi data.
- **Perbaikan Rute**: Memperbaiki eror rute `destroy` yang sebelumnya tidak terdaftar.

### Improved — Standarisasi Kewajiban Berkas Pendukung
- **Wajib Upload**: Semua kolom unggahan berkas pendukung pada seluruh jenis surat kini bersifat wajib diisi (*mandatory*) untuk memastikan kelengkapan berkas administrasi.
- **Pengecualian SPN**: Khusus untuk formulir **SPN (Surat Nikah)**, bagian **Lampiran Tambahan** (seperti Akta Cerai, Izin Poligami, dll) tetap bersifat **opsional** agar tidak menghambat pemohon yang tidak memerlukan dokumen tersebut.
- **Sinkronisasi Validasi**: Penyesuaian aturan validasi di sisi server (*backend*) agar selaras dengan indikator wajib pada antarmuka pengguna (*frontend*).

### Instruksi Deployment (Migration)
Untuk menerapkan fitur penghapusan, izin baru, dan sinkronisasi validasi dokumen di server, wajib menjalankan perintah migrasi berikut:

```bash
php artisan migrate --path=database/migrations/2026_04_06_014616_add_delete_permohonan_permission.php
```

---

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
