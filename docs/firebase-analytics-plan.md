# Pasang Google Analytics (GA4 via Firebase) untuk Tracking Traffic & Usage

> Status: **Rencana — belum diimplementasikan.** Dokumen ini menyimpan hasil planning supaya bisa dieksekusi nanti.

## Context

Tujuan akhir adalah mengukur kebutuhan server ke depan. Firebase Analytics / GA4 hanya menangkap data dari sisi browser (jumlah pengunjung, jam ramai, device, fitur yang sering dipakai) — bukan beban server (CPU/RAM/DB/queue). Diputuskan untuk memasang **Firebase Analytics dulu** sebagai langkah pertama (lihat pola traffic & fitur favorit), monitoring server-side (resource usage, response time, queue WA) menyusul belakangan sebagai tahap terpisah.

Saat ini project **belum punya analytics/tracking apa pun** — tidak ada di composer.json, package.json, .env.example, config/services.php, atau layout blade manapun. Jadi ini implementasi dari nol.

Scope yang disepakati:
- Dipasang di **portal publik DAN admin dashboard** (dua layout terpisah).
- Selain pageview otomatis, tambahkan **custom event** untuk aksi penting: submit permohonan, cek tracking status, dan download surat — supaya kelihatan fitur mana yang paling sering dipakai (relevan untuk planning kapasitas, karena generate PDF & kirim WA notifikasi adalah proses berat).

## Prasyarat (dilakukan manual, bukan kode)

Buat Firebase project (atau langsung GA4 property) di Firebase/Google Analytics console untuk mendapatkan **Measurement ID** (format `G-XXXXXXXXXX`). Isi sebagai placeholder dulu di `.env.example`, lalu isi nilai aslinya di `.env`.

## Implementasi

### 1. Config & Env
- `config/services.php` — tambah block baru mengikuti pola existing (`'claude'`, `'whatsapp'`, dst):
  ```php
  'firebase' => [
      'measurement_id' => env('FIREBASE_MEASUREMENT_ID'),
  ],
  ```
- `.env.example` — tambah section baru dengan header komentar mengikuti konvensi (`# W H A T S A P P`, dst):
  ```
  # Google Analytics (Firebase)
  FIREBASE_MEASUREMENT_ID=
  ```

### 2. Tracking script di kedua layout
Tambahkan snippet gtag.js standar (conditional, hanya render kalau `config('services.firebase.measurement_id')` terisi — supaya tidak error di environment lokal/testing tanpa ID):

- `resources/views/layouts/landing.blade.php` — sisipkan di `<head>`, setelah script SweetAlert2 (sekitar baris 18), sebelum config Tailwind custom.
- `resources/views/layouts/app.blade.php` — sisipkan di posisi setara pada `<head>` (struktur head lebih sederhana, pola sama).

Snippet (sama untuk kedua file):
```blade
@if(config('services.firebase.measurement_id'))
<script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.firebase.measurement_id') }}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '{{ config('services.firebase.measurement_id') }}');
</script>
@endif
```

### 3. Custom events
Pakai `@push('scripts')` (kedua layout sudah punya `@stack('scripts')` — `landing.blade.php:244` dan `app.blade.php:93`, jadi tinggal manfaatkan, tidak perlu ubah layout untuk ini). Tambahkan event di titik-titik kunci, dibungkus pengecekan `typeof gtag !== 'undefined'` supaya aman kalau measurement ID belum diisi:

- **`permohonan_submitted`** — di `resources/views/user/layanan/index.blade.php`, pada blok yang menampilkan flash message `session('success_application')` (mengikuti pola blok error yang sudah ada di sekitar baris 126-137 `create_public.blade.php`, tapi event difire di halaman tujuan redirect setelah submit sukses).
- **`tracking_status_viewed`** — di `resources/views/user/permohonan/track.blade.php`, dalam blok `@if(isset($permohonan))` (sekitar baris 28), kirim `status` dan jenis surat (jangan kirim `track_token` mentah sebagai event param — itu data identifiable, cukup kirim status/jenis surat untuk agregat).
- **`surat_downloaded`** — di halaman yang sama (`track.blade.php`, sekitar baris 86-92) tempat link download (`route('layanan.surat.tracking.download', ...)`) muncul, fire event saat link tersebut diklik (pakai `onclick` attribute kecil, bukan reload-detection, supaya akurat).

Catatan privasi: jangan kirim track_token, nomor surat, atau data pribadi pemohon sebagai event parameter — hanya kirim data agregat (jenis surat, status, timestamp) supaya tetap anonim di GA4.

## Verifikasi

1. Isi `FIREBASE_MEASUREMENT_ID` di `.env` lokal dengan ID dummy/real, jalankan `php artisan config:clear`.
2. Buka beberapa halaman portal (`/`, form ajukan surat, halaman tracking) dan admin dashboard — cek di browser DevTools Network tab bahwa request ke `googletagmanager.com/gtag/js` terkirim, dan tidak ada error JS console.
3. Submit satu permohonan surat dummy → cek event `permohonan_submitted` muncul (pakai GA4 DebugView di Firebase/Analytics console, atau cek `dataLayer` array via console browser).
4. Cek tracking status dengan track_token valid → pastikan event `tracking_status_viewed` terkirim tanpa membawa track_token sebagai parameter.
5. Klik link download surat → pastikan event `surat_downloaded` terkirim sebelum file ter-download.
6. Pastikan tidak ada regresi di environment test (`FIREBASE_MEASUREMENT_ID` kosong di `.env.testing`/CI) — layout harus tidak merender script gtag sama sekali (cek dengan `php artisan test`, tidak ada blade error).
