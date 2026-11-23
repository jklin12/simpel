# Revamp Halaman Publik Mobile: Beranda, Struktur Organisasi, Daftar Layanan, Download Template

Status: **screenshot Before selesai dan sudah ada di Figma. Layar After versi Figma belum dibuat** karena kuota panggilan Figma MCP (plan Starter) habis. **Sebagai gantinya, layar After sudah dibuat sebagai mockup HTML di repo: [ui-mockups/](ui-mockups/README.md)** (beranda, struktur organisasi, daftar layanan, download template, plus preview PNG). Belum ada perubahan kode aplikasi.
Lanjutan dari [wizard-mobile-ui.md](wizard-mobile-ui.md) (wizard form permohonan dan cek status).

## Figma

File: https://www.figma.com/design/QXRzI6LBR1RHvpsKwETwaV
(nama: "Simpel - Revamp Halaman Publik (Mobile)", file key `QXRzI6LBR1RHvpsKwETwaV`; plan Starter, maksimal 3 halaman per file)

| Halaman Figma | Node ID | Isi sekarang |
|---|---|---|
| Before | `0:1` | 5 screenshot produksi (belum dirapikan atau diberi label) |
| After | `1:2` | kosong |
| Komponen | `1:3` | kosong |

Screenshot di halaman Before (node masih di posisi bawaan, belum diubah ukurannya ke 375px):

| Node | Halaman | URL produksi |
|---|---|---|
| `1:4` | Beranda | `/` |
| `1:5` | Struktur organisasi | `/struktur-organisasi` |
| `1:6` | Daftar layanan | `/layanan` |
| `1:7` | Popup WhatsApp di daftar layanan | `/layanan` (saat dibuka) |
| `1:8` | Download template | `/layanan/surat-menyurat/download-template` |

## Cara screenshot Before diambil

- Sumber: produksi `https://simpel-bjb.id/`, hanya dibaca (tanpa login atau kirim data), tanggal 23 November 2026.
- Jendela Chrome tidak bisa dipersempit ke 375px, jadi tiap halaman dimuat dalam **iframe selebar 375px**, beberapa iframe berdampingan, masing-masing digulir ke irisan berbeda. Hasilnya disambung menjadi satu gambar panjang per halaman.
- Navbar dibuat tidak `fixed` agar tidak berulang di tiap irisan. Popup WhatsApp di `/layanan` ditutup untuk gambar daftar, dan direkam terpisah.
- Resolusi sekitar 0,94x, jadi agak lembut. Untuk hasil lebih tajam ambil ulang dengan DevTools device mode (iPhone SE 375x667, DPR 2) lalu "Capture full size screenshot".

## Temuan Before (tampilan HP 375px)

Tinggi diukur dari `scrollHeight`; "layar" dihitung dengan tinggi viewport 812px (perkiraan).

| Halaman | Tinggi | Masalah yang terlihat |
|---|---|---|
| Beranda | sekitar 3.627px, 4,5 layar | Hero slider setinggi satu layar dengan judul berbeda tiap slide; panah slider hilang di HP, hanya titik kecil; tiga kartu aksi ditumpuk; gambar berita masih placeholder; footer panjang |
| Struktur organisasi | sekitar 2.462px, 3 layar | **Bagan lebar tetap**: kartu terpotong di sisi kanan, area kosong besar, hanya 2 kartu terlihat, teks jabatan sangat kecil, perlu scroll horizontal; judul dan breadcrumb tampak bertumpuk |
| Daftar layanan | sekitar 8.820px, 10,9 layar | 17 kartu satu kolom tanpa pencarian; ikon huruf generik "S" di semua kartu; judul panjang sampai 5 baris; infografis alur berupa gambar dengan teks kecil |
| Popup WhatsApp (`/layanan`) | menutupi halaman | Gambar penuh menutupi daftar layanan, teks dalam gambar kecil, harus ditutup dulu sebelum memilih layanan |
| Download template | sekitar 4.379px, 5,4 layar | Judul surat menyempit 3-4 baris (terhimpit badge dan panah); deskripsi terpotong; tombol Download kecil dan tidak penuh lebar; teks menyebut "Kelurahan", bukan Kecamatan; tanpa pencarian |

Di semua halaman: footer 4 kolom sangat panjang di HP dengan tautan layanan yang tidak aktif (SKCK, Surat Domisili, Surat Kematian, Surat Kelahiran).

## Temuan kode (dasar `hotfix/v1.0.19`)

Semua halaman publik, memakai `resources/views/layouts/landing.blade.php`.

| Halaman | Route dan URL | Controller | View |
|---|---|---|---|
| Beranda | `home`, `/` | `Web\PortalPublikController@index` | `portal/index.blade.php` |
| Struktur organisasi | `struktur-organisasi` | `PortalPublikController@strukturOrganisasi` | `portal/struktur-organisasi.blade.php` |
| Daftar layanan | `layanan.index`, `/layanan` | `Layanan\ServiceController@index` | `services/index.blade.php` |
| Download template | `layanan.surat.template.index` | `Layanan\TemplateSuratPublikController@index` | `portal/template-surat/index.blade.php` |

- **Struktur organisasi:** bagan memakai lebar tetap `w-[1050px]` di dalam `overflow-x-auto`, label `text-[9px]`, posisi disusun lewat pencocokan substring jabatan (camat, sekre, fungsional, pemerintahan, dan sebagainya), dan kelas `.org-line` dipakai 10 kali tanpa CSS. Data asli hierarkis (`PortalStrukturOrganisasi`: `nama`, `jabatan`, `foto`, `parent_id`, `urutan`). Partial rekursif `portal/partials/struktur-node.blade.php` dan `getTreeData()` sudah ada tetapi tidak dipakai.
- **Daftar layanan:** data dari `JenisSurat` aktif dan `Kelurahan` (kecamatan `6372010`); modal pilih kelurahan; warna kartu di-hard-code per kode (SKTM, SKTMR, SKBM); popup dari `LayananPopupService`; tanpa pencarian atau filter.
- **Download template:** `TemplateSurat` per `JenisSurat` aktif, accordion Alpine, tiap template punya `syarat`.
- **Beranda:** `PortalSlider` aktif dan 3 `PortalBerita` terbaru; hero `min-h-[90vh]` plus `pt-[110px]`.
- **Layout global:** navbar `fixed` setinggi sekitar 96px (`py-4` + `h-16`), tiap halaman mengompensasi `pt-[110px]`; menu mobile tidak punya FAQ dan Cek Status; pengecekan aktif `routeIs('portal.*')` tidak pernah cocok karena nama route tanpa prefiks itu; footer punya banyak tautan `#`.

## Rancangan After (belum dibuat)

Tiap halaman 375x812, berdampingan dengan Before. Komponen bersama mengikuti gaya file wizard: font Outfit, `primary` biru `#2563eb`, kartu `rounded-2xl`.

- **Komponen bersama:** navbar mobile ringkas (logo dan hamburger, drawer lengkap termasuk FAQ dan Cek Status), footer ringkas dengan accordion, kartu layanan, kartu orang, tombol dan field.
- **Beranda:** hero pendek dengan swipe dan 2 CTA besar (Ajukan Surat, Cek Status); baris aksi cepat berikon (Peta, Template, FAQ); layanan populer scroll horizontal; carousel berita; tautan ke struktur dan FAQ.
- **Struktur organisasi:** pohon vertikal bertingkat (Camat, Sekcam, Kasi/Fungsional) dengan kartu orang berfoto dan accordion per bidang, tanpa scroll horizontal. Dibangun dari `parent_id` dan `urutan`, bukan slot substring.
- **Daftar layanan:** kolom pencarian dan chip kategori; kartu ringkas satu baris; bottom sheet pilih kelurahan dengan pencarian; infografis diganti langkah "Alur" 4 langkah; popup WhatsApp diganti banner yang bisa dilewati.
- **Download template:** pencarian; accordion per jenis surat; kartu template dengan ikon file, ukuran, dan tombol Unduh penuh lebar; syarat dilipat.
- **Tabel perubahan:** per halaman, masalah Before, solusi After, dan file Blade yang terdampak, termasuk catatan layout global di atas.

## Hambatan: kuota Figma MCP

- Pekerjaan terhenti dengan pesan "tool call limit on the Starter plan".
- Menurut dokumentasi Figma MCP (https://developers.figma.com/docs/figma-mcp-server/rate-limits-access/): seat View/Collab maksimal **20 panggilan per bulan** di semua plan; seat Full/Dev tidak tersedia di Starter; Professional 200 per hari dan 10 per menit; Organization 200 per hari dan 15 per menit; Enterprise 600 per hari dan 20 per menit. Figma dapat mengubah batas ini.
- Dokumen tidak menyebut tanggal reset maupun cara melihat sisa kuota. Akun ini tercatat Starter dengan seat "Full" (`whoami`), yang berbeda dari tabel dokumen, jadi aturan yang berlaku di akun ini belum pasti.
- Dugaan: reset bulanan, mungkin awal Desember 2026. Ini belum dikonfirmasi.
- Batas 3 halaman per file di Starter juga berlaku, jadi menambah halaman bukan solusi.

## Hasil sementara: mockup After di repo

Dibuat 23 November 2026 sebagai pengganti prototipe Figma. Detail, perbandingan tinggi halaman, pemetaan ke Blade, dan cara render ulang ada di [ui-mockups/README.md](ui-mockups/README.md). Ringkasan tinggi halaman pada 375px (Before dari produksi, After dari mockup dengan data contoh):

| Halaman | Before | After |
|---|---|---|
| Beranda | sekitar 3.627px | 1.850px |
| Struktur organisasi | sekitar 2.462px (plus scroll horizontal) | 1.458px |
| Daftar layanan | sekitar 8.820px | 2.712px |
| Download template | sekitar 4.379px | 1.948px |

Angka After memakai data contoh dan akan berubah dengan data asli.

## Langkah lanjutan

0. Tinjau mockup di [ui-mockups/](ui-mockups/index.html) bersama tim; revisi langsung di HTML-nya (lalu jalankan `render-previews.py`).
1. Saat kuota pulih (atau setelah upgrade ke Professional dengan seat Full), coba satu panggilan Figma untuk memastikan.
2. Rapikan halaman Before: ubah ukuran tiap screenshot ke lebar 375px, beri judul, URL, dan catatan masalah.
3. Bangun komponen bersama di halaman Komponen, lalu layar After empat halaman di halaman After. Gabungkan banyak pekerjaan dalam satu panggilan untuk menghemat kuota.
4. Tambah tabel perubahan, tinjau bersama tim, lalu baru implementasi Blade dari `hotfix/v1.0.19`.
5. Alternatif tanpa kuota Figma: buat layar After sebagai SVG/HTML lokal dan tarik ke halaman After (hasil berupa layer biasa, tanpa komponen).
