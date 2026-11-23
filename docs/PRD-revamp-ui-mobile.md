# PRD: Revamp UI Mobile-First Portal Simpel Landasan Ulin

| | |
|---|---|
| Status | Draft 0.1 (menunggu tinjauan) |
| Tanggal | 23 November 2026 |
| Basis kode | `hotfix/v1.0.19` (yang dipakai server) |
| Lingkup | Frontend publik (Blade, Tailwind, Alpine). Tidak ada perubahan alur bisnis backend. |
| Acuan desain | Figma wizard dan cek status: https://www.figma.com/design/VA5cBuxMVStvwSBexwfGhE · Figma screenshot Before: https://www.figma.com/design/QXRzI6LBR1RHvpsKwETwaV · Mockup HTML: [ui-mockups/](ui-mockups/README.md) |
| Dokumen terkait | [wizard-mobile-ui.md](wizard-mobile-ui.md), [revamp-halaman-publik-mobile.md](revamp-halaman-publik-mobile.md) |

Bagian yang berupa **usulan** (target angka, kategori, urutan rilis) ditandai dan perlu disetujui. Hal yang belum diketahui ada di bagian Pertanyaan terbuka.

---

## 1. Ringkasan

Portal publik Simpel dipakai warga terutama dari HP, tetapi tata letaknya masih berbasis desktop: form permohonan berupa satu halaman sangat panjang, bagan struktur organisasi memaksa scroll horizontal, dan daftar layanan menumpuk 17 kartu dalam satu kolom. Revamp ini membuat enam area antarmuka mobile-friendly tanpa mengubah logika backend:

1. Form permohonan surat (wizard bertahap, 17 jenis surat aktif + form dinamis)
2. Cek status permohonan
3. Beranda
4. Struktur organisasi
5. Daftar layanan surat
6. Download template surat

Plus perbaikan layout global (navbar dan footer) yang dipakai semuanya.

## 2. Latar belakang dan masalah

Data terukur dari produksi `https://simpel-bjb.id/` pada viewport 375px (23 November 2026, dari `scrollHeight`; "layar" dihitung dengan tinggi 812px, perkiraan):

| Area | Masalah terukur / terlihat |
|---|---|
| Daftar layanan | Sekitar 8.820px (10,9 layar). 17 kartu satu kolom tanpa pencarian; ikon huruf generik; judul sampai 5 baris; infografis alur berupa gambar bertulisan kecil; popup WhatsApp penuh layar menutupi halaman saat dibuka |
| Download template | Sekitar 4.379px (5,4 layar). Judul surat menyempit 3-4 baris karena terhimpit badge dan panah; tombol Download kecil dan tidak penuh lebar |
| Struktur organisasi | Sekitar 2.462px. Bagan lebar tetap 1.050px (`w-[1050px]`): kartu terpotong di kanan, scroll horizontal, label `text-[9px]` |
| Beranda | Sekitar 3.627px (4,5 layar). Hero `min-h-[90vh]`; panah slider hilang di HP; gambar berita placeholder |
| Form permohonan | Satu halaman panjang berisi kartu bernomor; SPN sekitar 689 baris dengan 14 berkas; satu `POST` multipart di akhir |
| Cek status | Seluruh isi `data_permohonan` ditampilkan sekaligus; grid berkas dan timeline panjang; tombol Unduh/Revisi berada di tengah halaman |
| Layout global | Navbar `fixed` sekitar 96px dengan kompensasi `pt-[110px]` di tiap halaman; menu mobile tanpa FAQ dan Cek Status; penanda halaman aktif (`routeIs('portal.*')`) tidak pernah cocok; footer 4 kolom panjang dengan tautan mati (`#`, SKCK, Surat Domisili, Surat Kelahiran, dan lainnya) |

Dampak: warga (banyak yang kurang terbiasa dengan form panjang) mudah menyerah, salah mengisi, atau kesulitan menemukan layanan dan statusnya.

## 3. Tujuan dan non-tujuan

### Tujuan
- **G1.** Setiap langkah form permohonan muat sekitar satu layar HP; tidak perlu scroll panjang.
- **G2.** Tidak ada konten yang memaksa scroll horizontal pada lebar 320-430px.
- **G3.** Layanan, status, dan template dapat ditemukan dan dipakai dengan satu tangan: target sentuh minimal 44px, aksi utama (Kirim, Unduh, Revisi) selalu terjangkau.
- **G4.** Mengurangi panjang halaman publik secara signifikan (target usulan di bagian 7).
- **G5.** Tidak ada regresi pada pengiriman permohonan, validasi, unggah berkas, OCR KTP, alur revisi, dan penomoran surat.

### Non-tujuan
- Redesain desktop (di atas `lg` tampilan lama harus tetap berfungsi; hanya diperiksa agar tidak rusak).
- Perubahan panel admin, alur persetujuan, penomoran surat, notifikasi WhatsApp, atau PDF.
- Jenis surat baru, dukungan multi-kecamatan, autentikasi warga, atau fitur akun.
- Mengganti stack (tetap Blade, Tailwind CDN, Alpine, TomSelect, SweetAlert2).

## 4. Pengguna

| Persona | Kebutuhan utama |
|---|---|
| Warga pemohon (pengguna utama, hampir semua lewat HP) | Mengajukan surat tanpa antre, tahu apa yang harus disiapkan, memantau status, mengunduh surat jadi |
| Warga yang ditolak | Memahami alasan dan memperbaiki permohonan dengan cepat |
| Warga yang mencari info | Melihat daftar layanan, template, struktur organisasi, dan berita |
| Admin/petugas | Tidak terdampak langsung; data yang mereka kelola (slider, berita, struktur, template, popup) tetap sama |

## 5. Ruang lingkup dan kebutuhan

Kode kebutuhan: FR = fungsional, NFR = non-fungsional. Prioritas: **P0** wajib, **P1** penting, **P2** opsional.

### 5.1 Layout global (`layouts/landing.blade.php`)
- **FR-GLB-01 (P0)** Navbar menempel setinggi sekitar 56px (logo dan hamburger); hapus kompensasi `pt-[110px]` per halaman.
- **FR-GLB-02 (P0)** Drawer menu memuat semua tujuan, termasuk FAQ dan Cek Status, dengan baris minimal 48px dan tombol "Ajukan Surat".
- **FR-GLB-03 (P1)** Perbaiki penanda halaman aktif yang tidak pernah cocok (`routeIs('portal.*')` memakai nama route yang tidak ada).
- **FR-GLB-04 (P0)** Footer ringkas: info kontak tampil, daftar tautan dalam accordion; hapus tautan mati dan tautan ke jenis surat tidak aktif.

### 5.2 Form permohonan surat (wizard)
Berlaku untuk 17 jenis surat aktif: ROIPK, SDNH, SKB, SKBM, SKDK, SKDKO, SKG, SKJD, SKM, SKP, SKSI, SKTM, SKTMR, SPKDK, SPKH, SPN, SPRIK; ditambah form dinamis untuk jenis baru dari admin. Jenis tidak aktif (SKCK, SKD, SKDU, SKL, SKU) di luar lingkup.

- **FR-WIZ-01 (P0)** Form dipecah menjadi langkah, maksimal sekitar 5-6 field per langkah. Pola langkah per jenis ada di [wizard-mobile-ui.md](wizard-mobile-ui.md) (pola A, A+, B, C, D, E, dinamis).
- **FR-WIZ-02 (P0)** Header menampilkan nama surat, "Langkah n dari N", dan progress bar. Bottom bar tetap (Kembali/Lanjut) dengan tombol setinggi 48px.
- **FR-WIZ-03 (P0)** Satu kolom di HP; keyboard sesuai tipe (numerik untuk NIK dan WhatsApp, date picker untuk tanggal).
- **FR-WIZ-04 (P0)** Scan KTP (OCR) berada di langkah pertama dan mengisi field langkah-langkah berikutnya seperti perilaku sekarang.
- **FR-WIZ-05 (P0)** Unggah berkas sebagai daftar kompak (nama, status terunggah, tombol Unggah/Ganti). Panduan lampiran dari `attachment_guides` tampil di bottom sheet.
- **FR-WIZ-06 (P0)** Berkas kondisional (khususnya SPN: pernah menikah, poligami, usia di bawah ketentuan) muncul lewat toggle sesuai status.
- **FR-WIZ-07 (P1)** Bagian jarang dipakai (pernyataan, keterangan tambahan) berada di accordion.
- **FR-WIZ-08 (P0)** Langkah terakhir "Tinjau & Kirim": ringkasan per bagian dengan tombol "Ubah" yang kembali ke langkah terkait; pengiriman hanya dari langkah ini.
- **FR-WIZ-09 (P0)** Validasi per langkah: tidak boleh lanjut jika field wajib kosong atau tidak valid. Jangan menyembunyikan input `required` dengan `display:none` (memblokir submit).
- **FR-WIZ-10 (P0)** Setelah redirect error dari server (`withInput()`), wizard terbuka di langkah pertama yang berisi error, dengan pesan error pada field. Field memakai `old()`; berkas tidak bisa dipulihkan, tampilkan petunjuk unggah ulang.
- **FR-WIZ-11 (P0)** Semua atribut `name` field, `enctype="multipart/form-data"`, serta field tersembunyi `jenis_surat_id` dan `kelurahan_id` tetap seperti sekarang, sehingga `StorePermohonanRequest`, `PermohonanController::store`, `handleFileUploads`, dan alur revisi tidak berubah.
- **FR-WIZ-12 (P0)** Hook yang ada tetap berfungsi: OCR Alpine (`x-model`, `x-ref`, `PermohonanController@ocrKtp`), TomSelect `.select2-pekerjaan`, serta injeksi panduan lampiran yang dicocokkan lewat `name` input file.
- **FR-WIZ-13 (P0)** Template statis SKTM, SKTMR, dan SKM tetap statis sesuai `CLAUDE.md`; jenis baru tetap lewat sistem dinamis (`required_fields`).
- **FR-WIZ-14 (P1)** Halaman revisi (`revisi.blade.php`) memakai kerangka wizard yang sama dengan data terisi.
- **FR-WIZ-15 (P2)** Simpan draf pengisian di peramban agar tidak hilang bila halaman tertutup (tidak termasuk prototipe; lihat Pertanyaan terbuka).

### 5.3 Cek status permohonan (`user/permohonan/track.blade.php`)
- **FR-TRK-01 (P0)** Halaman pencarian: input kode tracking besar, tombol Cek Status, petunjuk di mana kode didapat, tautan "Ajukan permohonan".
- **FR-TRK-02 (P0)** State error "Kode tracking tidak ditemukan." tampil di bawah input.
- **FR-TRK-03 (P0)** Hasil menampilkan kode (dengan tombol salin), jenis surat, tanggal pengajuan, dan status (Menunggu, Sedang Diproses, Selesai, Ditolak, plus badge revisi) di bagian atas.
- **FR-TRK-04 (P0)** Riwayat status (timeline) tampil sebelum detail; catatan approval tampil di bawah langkah terkait.
- **FR-TRK-05 (P0)** Status Selesai dengan berkas tersedia: kartu "siap diunduh", nomor surat, dan tombol **Unduh Surat** menempel di bawah layar.
- **FR-TRK-06 (P0)** Status Ditolak: kartu alasan penolakan dan tombol **Revisi & Ajukan Ulang** menempel di bawah layar.
- **FR-TRK-07 (P0)** Data pemohon dan berkas lampiran berada di accordion (tertutup secara bawaan); **NIK tetap dimasking** seperti sekarang.
- **FR-TRK-08 (P1)** Event analytics yang ada (`tracking_status_viewed`, `surat_downloaded`) tetap terkirim.

### 5.4 Beranda (`portal/index.blade.php`)
- **FR-HOM-01 (P0)** Hero dipendekkan (sekitar 300px) dengan 2 CTA: Ajukan Surat dan Cek Status; slider mendukung swipe dan penanda halaman.
- **FR-HOM-02 (P0)** Baris aksi cepat berikon: Ajukan, Cek Status, Template, Peta.
- **FR-HOM-03 (P1)** Kolom cek status inline (input kode dan tombol).
- **FR-HOM-04 (P1)** Layanan populer sebagai carousel horizontal dengan tautan "Lihat semua".
- **FR-HOM-05 (P0)** Berita terbaru sebagai carousel horizontal; gambar kosong memakai placeholder yang konsisten.
- **FR-HOM-06 (P1)** Kartu teaser ke Struktur Organisasi, FAQ, dan bantuan WhatsApp.

### 5.5 Struktur organisasi (`portal/struktur-organisasi.blade.php`)
- **FR-ORG-01 (P0)** Ganti bagan lebar menjadi pohon vertikal: Camat, Sekretaris Kecamatan, lalu bidang/seksi sebagai accordion berisi anggota.
- **FR-ORG-02 (P0)** Dibangun dari data hierarki (`PortalStrukturOrganisasi`: `parent_id`, `urutan`), bukan pencocokan substring jabatan dan slot tetap.
- **FR-ORG-03 (P0)** Tidak ada scroll horizontal; teks minimal 11px.
- **FR-ORG-04 (P1)** Foto dengan teks alternatif; jika foto kosong tampilkan avatar placeholder.
- **FR-ORG-05 (P1)** Data kosong menampilkan pesan yang jelas, bukan area kosong.

### 5.6 Daftar layanan surat (`services/index.blade.php`)
- **FR-SVC-01 (P0)** Kolom pencarian yang menempel di bawah navbar, dan chip kategori.
- **FR-SVC-02 (P0)** Daftar dikelompokkan per kategori dengan baris ringkas (kode, nama, panah); satu warna per kategori, bukan warna di-hardcode per kode surat.
- **FR-SVC-03 (P0)** Memilih layanan membuka **bottom sheet pilih kelurahan** (dengan pencarian) lalu melanjutkan ke `layanan.surat.ajukan` dengan `service_id` dan `kelurahan_id` seperti sekarang.
- **FR-SVC-04 (P1)** Infografis alur diganti langkah "Alur pengajuan" 4 langkah (teks, bukan gambar).
- **FR-SVC-05 (P1)** Popup WhatsApp tidak lagi menutup halaman penuh; diganti banner yang bisa dilewati (konten tetap bisa dikelola admin lewat `LayananPopupService`).
- **FR-SVC-06 (P1)** Modal sukses sesi (`success_application`) dapat digulir dan muat di layar kecil.

### 5.7 Download template surat (`portal/template-surat/index.blade.php`)
- **FR-TPL-01 (P0)** Kolom pencarian template.
- **FR-TPL-02 (P0)** Accordion per jenis surat; judul tidak terhimpit badge dan panah.
- **FR-TPL-03 (P0)** Kartu template: ikon, judul, nama berkas, tipe dan ukuran berkas, tombol **Unduh** penuh lebar.
- **FR-TPL-04 (P1)** Syarat dokumen (`syarat`) dilipat dalam accordion; langkah "Cara menggunakan template" diringkas dengan tombol "Ajukan online".
- **FR-TPL-05 (P0)** Rute dan perilaku unduh (`layanan.surat.template.download`, 404 bila nonaktif atau berkas tidak ada) tidak berubah.

### 5.8 Kebutuhan non-fungsional
- **NFR-01** Berfungsi baik pada lebar 320-430px; tidak ada scroll horizontal pada halaman (kecuali carousel yang memang dirancang).
- **NFR-02** Target sentuh minimal 44px; teks isi minimal 13px, keterangan/badge minimal 11px.
- **NFR-03** Kontras teks memenuhi WCAG AA; gambar bermakna punya teks alternatif; field punya label.
- **NFR-04** Gambar di bawah layar pertama memakai `loading="lazy"`; tidak menambah dependensi JS baru yang besar.
- **NFR-05** Peramban target: Chrome dan Safari mobile versi terbaru dan satu sebelumnya.
- **NFR-06** Tampilan desktop (di atas `lg`) tidak rusak dan tetap dapat dipakai.
- **NFR-07** Seluruh teks antarmuka berbahasa Indonesia, konsisten dengan istilah yang ada.

## 6. Batasan dan keputusan yang sudah ada

- Repo bercabang dua jalur: `main` dan `hotfix/v1.0.19`. Server memakai jalur hotfix. `main` memiliki 10 commit yang tidak ada di hotfix (multi-kecamatan, pindah wilayah, aturan OCR, pencegahan spam SKTM, penghapusan dynamic form, dan lainnya) dan hotfix memiliki 1 commit yang tidak ada di `main` (`3f653a6`, status Plt/Plh camat). Implementasi dikerjakan dari `hotfix/v1.0.19`.
- Penomoran surat, PDF, notifikasi, dan persetujuan tidak disentuh.
- Template SKTM/SKTMR/SKM tetap statis; edit manual bila ada perubahan field (lihat `CLAUDE.md`).
- Pengujian: PHPUnit dengan MySQL langsung dan `DatabaseTransactions` (bukan `RefreshDatabase`).
- Tailwind dipakai lewat CDN dengan konfigurasi inline di `layouts/landing.blade.php` (tidak ada `tailwind.config.js`).

## 7. Metrik keberhasilan

Baseline panjang halaman diukur dari produksi (bagian 2). Target berikut adalah **usulan**; angka After dari mockup memakai data contoh.

| Metrik | Baseline | Target (usulan) |
|---|---|---|
| Panjang daftar layanan di 375px | sekitar 8.820px | sekitar 2.700px atau lebih pendek (mockup: 2.712px) |
| Panjang download template | sekitar 4.379px | sekitar 1.950px atau lebih pendek (mockup: 1.948px) |
| Panjang beranda | sekitar 3.627px | sekitar 1.850px atau lebih pendek (mockup: 1.850px) |
| Panjang struktur organisasi | sekitar 2.462px + scroll horizontal | tanpa scroll horizontal (mockup: 1.458px) |
| Scroll vertikal per langkah wizard | 3-4 layar per halaman form (perkiraan SKTM) | maksimal 1-1,5 layar per langkah |
| Scroll horizontal pada halaman publik | ada (struktur organisasi) | nol |
| Tingkat penyelesaian permohonan (mulai form sampai kirim) | belum diukur | ditetapkan setelah baseline dipasang di analytics; target naik |
| Waktu rata-rata mengisi satu permohonan | belum diukur | turun dibanding baseline |
| Permohonan ditolak karena berkas/data salah | belum diukur dari data | turun (dilihat dari alasan penolakan) |

Pengukuran: ukur ulang `scrollHeight` pada 375px; analytics yang ada (GA/Firebase `measurement_id`) perlu ditambah event langkah wizard untuk baseline penyelesaian.

## 8. Rencana rilis (usulan)

Urutan didasarkan pada risiko rendah dulu dan dampak terbesar.

| Fase | Isi | Alasan |
|---|---|---|
| 0 | Layout global (navbar, drawer, footer) dan komponen Blade bersama (tombol, field, baris berkas, accordion, bottom sheet) | Dipakai semua halaman; mengurangi duplikasi kelas |
| 1 | Cek status dan download template | Halaman sederhana, tanpa perubahan input form |
| 2 | Daftar layanan, beranda, struktur organisasi | Pintu masuk utama; struktur memerlukan penyesuaian data/Blade |
| 3 | Wizard: SKTM dulu (pola A), lalu A+, B (SKM), C, D (SPN), E, dan form dinamis | Paling berisiko karena menyangkut validasi dan unggah; dikerjakan bertahap per pola dengan uji per jenis |

Tiap fase dirilis lewat branch terpisah dari `hotfix/v1.0.19` dan diuji di staging sebelum produksi.

## 9. Kriteria penerimaan (ringkas)

- Semua halaman pada lebar 320, 375, dan 430px: tidak ada scroll horizontal, teks tidak terpotong, target sentuh ≥44px.
- Wizard: tiap jenis surat aktif dapat diisi dan dikirim sampai selesai; data dan berkas yang tersimpan identik dengan yang dihasilkan form lama (`data_permohonan`, `PermohonanDokumen`, nomor permohonan, token tracking).
- Error validasi server mengarahkan ke langkah yang tepat dengan input terisi ulang.
- OCR KTP, TomSelect pekerjaan, panduan lampiran, deteksi permohonan ganda (NIK sama, jenis sama, hari sama), dan alur revisi tetap bekerja.
- Cek status: keempat status tampil benar; tombol Unduh hanya untuk status Selesai dengan berkas; NIK dimasking.
- Struktur organisasi tampil dari data hierarki; tidak bergantung pada pencocokan substring jabatan.
- Test PHPUnit yang ada tetap lulus; ditambah uji untuk alur yang berubah.

## 10. Risiko

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Template statis besar (SPN sekitar 689 baris) dipecah menjadi langkah | Regresi validasi atau unggah | Pertahankan `name`; uji kirim per jenis; rilis bertahap per pola |
| Hook OCR dan panduan lampiran terkait struktur DOM lama | Fitur berhenti bekerja | Uji OCR dan panduan lampiran di tiap jenis sebelum rilis |
| Jalur `main` dan `hotfix` bercabang | Konflik saat penggabungan | Kerjakan dari hotfix; rencanakan penggabungan terpisah |
| Data produksi tidak sesuai asumsi mockup (kategori, struktur, template) | Desain perlu penyesuaian | Validasi dengan data produksi sebelum Fase 2 |
| Tailwind CDN memengaruhi performa/kelas dinamis | Gaya tidak muncul | Gunakan kelas lengkap (tanpa string dinamis); cek tiap halaman |
| Prototipe Figma wizard hanya mencakup SKTM, SKM, SPN, dan dinamis | Jenis lain belum tervalidasi desainnya | Petakan ke pola terdekat; tinjau per jenis di Fase 3 |
| Mockup belum diuji di perangkat nyata | Masalah kontras atau sentuh baru ketahuan belakangan | Uji di perangkat nyata sebelum implementasi |

## 11. Pertanyaan terbuka

1. Kategori layanan (keterangan warga, kematian dan kehilangan, pernikahan, lembaga) hanyalah usulan. Perlu kolom di `jenis_surats` atau cukup pemetaan di kode? Siapa yang menentukan pengelompokan?
2. "Layanan populer" di beranda ditentukan manual (admin) atau dari jumlah permohonan?
3. Struktur organisasi: nama bidang dan jumlah tingkat pada data produksi belum dicek; bagan perlu mendukung kedalaman berapa?
4. Popup WhatsApp: tetap dikelola admin dengan gambar penuh, atau diganti banner teks? Siapa yang memutuskan isinya?
5. Template: teks "Kelurahan" di halaman saat ini, apakah benar untuk templat tingkat kelurahan, atau perlu disesuaikan ke Kecamatan?
6. Apakah perlu menyimpan draf form di peramban (FR-WIZ-15)? Ada pertimbangan privasi karena memuat NIK.
7. Peran halaman FAQ, Berita, dan Peta (belum direvisi di PRD ini) terhadap navigasi baru; apakah ikut direvisi pada fase berikutnya?
8. Target angka metrik dan baseline penyelesaian permohonan: siapa yang memegang akses analytics dan kapan baseline mulai dikumpulkan?
9. Prototipe Figma jenis surat lain (SKTMR, SKBM, SKP, SKJD, SKSI, SKG, SPKH, SKB, SDNH, jenis lembaga) baru berupa pemetaan; perlu layar penuh sebelum implementasi?

## 12. Lampiran: peta berkas

| Area | Blade / controller utama |
|---|---|
| Layout global | `resources/views/layouts/landing.blade.php` |
| Form permohonan | `resources/views/user/permohonan/create_public.blade.php`, `user/permohonan/types/*.blade.php` (17 statis + `dynamic`), `revisi.blade.php`; `app/Http/Controllers/Layanan/PermohonanController.php`; `app/Http/Requests/StorePermohonanRequest.php` |
| Cek status | `resources/views/user/permohonan/track.blade.php`; `app/Http/Controllers/Layanan/TrackingController.php`; route `layanan.surat.tracking*` |
| Beranda | `resources/views/portal/index.blade.php`; `Web\PortalPublikController@index` |
| Struktur organisasi | `portal/struktur-organisasi.blade.php`; `PortalPublikController@strukturOrganisasi`; model `PortalStrukturOrganisasi` |
| Daftar layanan | `resources/views/services/index.blade.php`; `Layanan\ServiceController@index` |
| Download template | `portal/template-surat/index.blade.php`; `Layanan\TemplateSuratPublikController` |
| Mockup | `docs/ui-mockups/*.html`, `docs/ui-mockups/previews/*.png` |
