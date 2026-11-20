# Revamp UI Mobile: Wizard Form Permohonan & Cek Status

Status: **desain prototipe selesai di Figma, implementasi kode belum dimulai.**
Dokumen ini merangkum apa yang sudah dikerjakan agar mudah dilanjutkan.

## Latar belakang

Form publik permohonan surat (`resources/views/user/permohonan/create_public.blade.php`) berupa satu halaman sangat panjang berisi kartu bernomor (SPN sekitar 689 baris dengan 14 berkas). Di HP warga harus scroll jauh. Tujuan revamp:

- Form menjadi **wizard bertahap**, tiap langkah muat sekitar 1 layar HP.
- Halaman **cek status** (`track.blade.php`) ikut mobile-friendly.

## Prototipe Figma

File: https://www.figma.com/design/VA5cBuxMVStvwSBexwfGhE
(nama: "Simpel - Wizard Permohonan Surat (Mobile)", file key `VA5cBuxMVStvwSBexwfGhE`, plan Starter, maksimal 3 halaman)

| Halaman Figma | Isi | Node ID |
|---|---|---|
| Komponen (`0:1`) | Field `2:7`, Select `2:11`, FileRow `2:17`, BottomBar `2:26`, Header `2:31` | `2:6` (kumpulan) |
| Layar Mobile (`2:4`) | Alur SKTM (5 langkah) | `3:269` |
| | Alur SKM (5 langkah) | `4:435` |
| | Alur Dinamis (3 langkah) | `4:580` |
| | Alur SPN (7 langkah + bottom sheet panduan lampiran) | `5:775` |
| | Alur Cek Status (6 layar) | `9:968` |
| Pemetaan (`2:5`) | Tabel pemetaan 17 jenis surat aktif | `6:2` |

Ukuran layar 375x812. Gaya mengikuti yang sudah ada: font **Outfit**, `primary` biru `#2563eb`, kartu `rounded-2xl`, input `rounded-lg` abu-abu.

## Prinsip desain wizard

- Satu langkah maksimal sekitar 5-6 field; kartu lama dipecah lebih kecil.
- Header ringkas dengan progress bar dan "Langkah n dari N".
- **Bottom bar tetap** (Kembali / Lanjut), tombol tinggi 48px.
- Satu kolom, input tinggi 48px, keyboard sesuai tipe (numerik untuk NIK dan WA).
- **Scan KTP (OCR) di langkah pertama.**
- Berkas berupa daftar kompak (ikon, nama, status, tombol Unggah/Ganti). Panduan lampiran muncul di **bottom sheet**.
- Berkas kondisional (SPN) muncul lewat toggle (pernah menikah, poligami, usia di bawah ketentuan).
- Bagian jarang dipakai ditaruh di accordion.
- Langkah terakhir **Tinjau & Kirim**: ringkasan per bagian dengan tombol "Ubah".
- Error ditampilkan per langkah dan wizard lompat ke langkah yang berisi error.

## Jenis surat aktif (dari `jenis_surats.is_active = 1`, 17 jenis)

ROIPK, SDNH, SKB, SKBM, SKDK, SKDKO, SKG, SKJD, SKM, SKP, SKSI, SKTM, SKTMR, SPKDK, SPKH, SPN, SPRIK.

Tidak aktif (5): SKCK, SKD, SKDU, SKL, SKU. Seeder-nya tanpa partial dan `required_fields` berupa string biasa; kemungkinan peninggalan lama, tidak termasuk prototipe.

Pemetaan ke pola (detail lengkap ada di halaman Pemetaan Figma):

| Pola | Jenis | Langkah |
|---|---|---|
| A, umum | SKTM, SKTMR, SKBM, SKP, SKJD | Identitas, Alamat & pekerjaan, Pengantar & keperluan, Berkas, Tinjau |
| A+, umum + 1 langkah | SKSI (data istri), SKG (data yang gaib), SPKH (data kehilangan), SKB (perjalanan) | Pola A ditambah satu langkah khusus |
| B | SKM | Pelapor, Almarhum, Kejadian, Berkas, Tinjau |
| C | SDNH | Pemohon, Pasangan, Rencana nikah & alasan, Berkas, Tinjau |
| D | SPN | Calon, Alamat & status, Ayah, Ibu, Berkas identitas, Berkas tambahan, Tinjau |
| E, lembaga | SKDKO, SKDK, SPKDK, SPRIK, ROIPK | Pemohon (OCR), Data lembaga, Berkas, Tinjau |
| Dinamis | jenis baru dari admin | Data permohonan, Berkas, Tinjau |

Layar penuh hanya dibuat untuk **SKTM, SKM, SPN, dan Dinamis**. Jenis lain baru berupa pemetaan di tabel.

## Halaman cek status (`resources/views/user/permohonan/track.blade.php`)

Masalah di mobile saat ini: seluruh isi `data_permohonan` ditampilkan sekaligus, grid berkas dan timeline panjang, tombol Unduh/Revisi berada di tengah halaman.

Rancangan (6 layar):
1. Masukkan kode tracking + petunjuk di mana kode didapat.
2. State error "Kode tracking tidak ditemukan."
3. Hasil **Diproses**: kode, status, riwayat; data pemohon dan berkas dalam accordion tertutup.
4. Hasil **Selesai**: nomor surat, kartu siap unduh, tombol **Unduh Surat (PDF)** menempel di bawah layar.
5. Hasil **Ditolak**: alasan penolakan, badge revisi, tombol **Revisi & Ajukan Ulang** menempel di bawah layar.
6. **Detail**: accordion Data pemohon (NIK tetap dimasking) dan Berkas lampiran terbuka.

Route terkait: `layanan.surat.tracking`, `tracking.search`, `tracking.download` di `routes/portal.php`; controller `app/Http/Controllers/Layanan/TrackingController.php`.

## Yang harus dipertahankan saat implementasi

- Semua atribut `name` field persis seperti sekarang, `enctype="multipart/form-data"`, hidden `jenis_surat_id` dan `kelurahan_id`.
- `old()` dan `@error` di setiap field. File tidak bisa di-repopulate, jadi tampilkan hint "unggah ulang".
- Validasi per langkah (jangan memakai `display:none` pada input `required`, karena akan memblokir submit). Setelah redirect error dari server, buka langkah yang berisi error.
- Hook yang ada: OCR Alpine (`x-model`, `x-ref`, `PermohonanController@ocrKtp`), TomSelect `.select2-pekerjaan`, serta injeksi panduan lampiran yang dicocokkan lewat `name` input file.
- Sisi server tidak berubah: `StorePermohonanRequest` (termasuk `fileFields()`), `PermohonanController::store`, `handleFileUploads`, dan alur revisi (`update`, `revisi.blade.php`) yang memakai nama field yang sama.
- Template statis SKTM/SKTMR/SKM tetap statis, sesuai `CLAUDE.md`. Jenis baru tetap lewat sistem dynamic.
- Tailwind dipakai lewat CDN dengan konfigurasi inline di `layouts/landing.blade.php` (tidak ada `tailwind.config.js`).

## Catatan dan asumsi

- Jumlah berkas di tabel pemetaan (misalnya "5-6" atau "sesuai form") adalah **perkiraan** dari ringkasan form, belum dicek per file.
- Perbandingan "sekarang sekitar 3-4 layar scroll" adalah perkiraan, belum diukur di HP.
- Data pada layar prototipe hanyalah contoh. Nama langkah di riwayat status dikarang; aslinya dari `step_name` pada approvals.
- Status "Menunggu" dan "Sedang Diproses" sama-sama kuning, mengikuti halaman sekarang.
- Hanya komponen dasar yang berupa component Figma; progress per layar diatur manual.
- Versi desktop belum dibuat (prioritas mobile).

## Langkah lanjutan

1. Review dan revisi prototipe bersama tim.
2. Pecah partial `types/*.blade.php` menjadi langkah wizard. Mulai dari SKTM sebagai pola umum (komponen Blade bersama untuk stepper, bottom bar, baris berkas, bottom sheet).
3. Terapkan ke jenis lain per pola (A+, B, C, D, E), lalu form dinamis.
4. Rombak `track.blade.php` sesuai 6 layar di atas.
5. Uji di perangkat nyata, termasuk alur error server dan revisi.
