# Security Review — Fitur Permohonan Surat

**Tanggal:** 2026-07-02
**Cakupan:** Alur permohonan surat end-to-end — form publik, validasi, upload file, penyimpanan, penyajian dokumen (admin & tracking), routing & otorisasi.
**Branch:** `release/v1.0.7`

---

## Ringkasan Eksekutif

Fondasi keamanan cukup baik: query pakai Eloquent (aman dari SQL injection), CSRF aktif via middleware `web`, validasi input lengkap per jenis surat, dan kegagalan notifikasi WhatsApp di-swallow dengan benar.

Namun ditemukan **2 masalah serius terkait kerahasiaan dokumen PII** (KTP, KK, buku nikah, akta) dan beberapa isu rate-limiting yang perlu segera ditangani.

**Prioritas tindakan:** (1) IDOR / broken access control → (2) pindahkan dokumen ke disk privat → (3) throttle `/ocr`.

| # | Severity | Temuan |
|---|----------|--------|
| 1 | 🔴 CRITICAL | Broken Access Control / IDOR pada dokumen & detail permohonan |
| 2 | 🔴 HIGH | Dokumen PII disimpan di disk `public` (akses tanpa login) |
| 3 | 🟠 MEDIUM | Tidak ada rate limiting di endpoint publik (`/ocr`, `/ajukan`, `/cek-status/search`) |
| 4 | 🟠 MEDIUM | Tracking token membuka seluruh PII pemohon |
| 5 | 🟡 LOW | Kebocoran pesan error internal ke user |
| 6 | 🟡 LOW | Validasi file bisa diperketat |

---

## 🔴 CRITICAL — Broken Access Control / IDOR pada dokumen & detail permohonan

Daftar permohonan **di-scope** per kelurahan di `app/Repositories/PermohonanSuratRepository.php:78`:

```php
if ($user->hasRole('admin_kelurahan')) {
    $query->where('kelurahan_id', $user->kelurahan_id);
}
```

Tapi akses **langsung by ID** tidak di-scope. `app/Services/PermohonanSuratService.php:51`:

```php
public function getPermohonanById($id) {
    return $this->repository->find($id);   // tidak ada cek kelurahan/kecamatan
}
```

Endpoint berikut memakai `getPermohonanById($id)` **tanpa verifikasi kepemilikan wilayah**:
`show`, `downloadDokumen`, `downloadLetter`, `edit`, `update`, `approve`, `reject`, `requestPerubahan`, `destroy`.

**Dampak:** admin_kelurahan A cukup mengganti angka ID di URL untuk melihat & mengunduh KTP/KK/buku nikah warga di kelurahan B, bahkan meng-approve/reject/hapus permohonannya. Contoh:

```
/admin/permohonan-surat/1523/dokumen/8801/download   ← ganti ID, tetap tembus
```

`downloadDokumen` (`app/Http/Controllers/Admin/Surat/PermohonanSuratController.php:275`) hanya mencocokkan `permohonan_surat_id` dengan `dokumenId`, tidak mengecek wilayah admin. `destroy` hanya cek permission `delete_permohonan`, bukan kepemilikan.

### Perbaikan

Tambahkan scoping otorisasi terpusat (di service atau lewat Policy):

```php
public function getPermohonanById($id)
{
    $permohonan = $this->repository->find($id);
    $user = auth()->user();

    if ($user->hasRole('admin_kelurahan')
        && $permohonan->kelurahan_id !== $user->kelurahan_id) {
        abort(403, 'Anda tidak berhak mengakses permohonan ini.');
    }
    if ($user->hasRole('admin_kecamatan')
        && optional($permohonan->kelurahan)->kecamatan_id !== $user->kecamatan_id) {
        abort(403);
    }
    return $permohonan;
}
```

Idealnya pakai `PermohonanPolicy` (`view`/`update`/`delete`) + `$this->authorize('view', $permohonan)` di tiap method controller agar konsisten dan dapat diuji.

---

## 🔴 HIGH — Dokumen PII disimpan di disk `public` (bisa diakses tanpa login)

Di `app/Http/Controllers/Layanan/PermohonanController.php:253` file diupload ke disk `public`:

```php
$path = $file->store('permohonan/' . $permohonan->id . '/dokumen', 'public');
```

Disk `public` (`config/filesystems.php:39`) ber-`visibility: public` dan di-symlink ke `public/storage`. Artinya **semua KTP/KK/buku nikah bisa diunduh siapa saja tanpa autentikasi** lewat URL:

```
https://.../storage/permohonan/{id}/dokumen/{namafile}
```

Tanpa lewat controller, tanpa cek role. Satu-satunya penghalang adalah nama file acak 40-karakter — itu **bukan kontrol akses** (URL bocor lewat history browser, header Referer, log, atau share, lalu tembus permanen).

Tidak ada yang butuh URL publik langsung: admin sudah mengunduh via route terkontrol `admin.permohonan-surat.download-dokumen`, dan halaman tracking tidak menampilkan link dokumen. Jadi disk `public` memang tidak diperlukan untuk berkas ini.

### Perbaikan

1. Ganti ke disk privat pada upload store & revisi:
   ```php
   $file->store('permohonan/'.$permohonan->id.'/dokumen', 'local');
   ```
   (`PermohonanController::handleFileUploads` dan `handleRevisiFileUploads`)
2. Gunakan `Storage::disk('local')` di `downloadDokumen` dan pada base64 pas foto SPN (`PermohonanSuratController.php:169`).
3. Migrasikan file lama dari `storage/app/public/permohonan` ke `storage/app/permohonan`.

Perubahan ini sekaligus menutup risiko PDF/HTML inline yang bisa dieksekusi browser saat diakses langsung.

---

## 🟠 MEDIUM — Tidak ada rate limiting di endpoint publik

Route publik di `routes/portal.php:38-43` tidak punya `throttle`:

- **`/ocr`** (`ocrKtp`) — tanpa login, **memanggil Claude API berbayar**. Rawan abuse/cost-DoS: spam request menguras kuota & tagihan API. **Paling mendesak.**
- **`/ajukan` (store)** — bisa dibanjiri permohonan sampah.
- **`/cek-status/search`** — memungkinkan brute-force `track_token`.

### Perbaikan

```php
Route::post('/ocr', [PermohonanController::class, 'ocrKtp'])
    ->middleware('throttle:10,1')->name('ocr');
Route::post('/ajukan', [PermohonanController::class, 'store'])
    ->middleware('throttle:20,1')->name('store');
Route::get('/cek-status/search', [TrackingController::class, 'search'])
    ->middleware('throttle:30,1')->name('tracking.search');
```

---

## 🟠 MEDIUM — Tracking token membuka seluruh PII pemohon

`TrackingController::search` (`app/Http/Controllers/Layanan/TrackingController.php:17`) mengembalikan data lengkap (nama, **NIK penuh**, alamat, no WA, seluruh `data_permohonan`) untuk token valid apa pun.

Entropi `Str::random(10)` uppercase (~36^10) besar sehingga brute force tak praktis, tapi tanpa throttle (lihat #3) tetap layak diperkuat. Pertimbangkan juga **masking NIK** di `resources/views/user/permohonan/track.blade.php:194` (mis. `3271••••••••1234`).

---

## 🟡 LOW — Kebocoran pesan error internal ke user

Beberapa tempat menampilkan `$e->getMessage()` mentah ke pengguna:

- `app/Http/Controllers/Layanan/PermohonanController.php:229` & `:371` — `'Terjadi kesalahan: ' . $e->getMessage()`
- `ocrKtp:561` — `'Gagal memproses OCR: ' . $e->getMessage()`

Berpotensi membocorkan detail SQL/path/internal. Sebaiknya `Log::error($e)` untuk detail, tampilkan pesan generik ke user.

---

## 🟡 LOW — Validasi file bisa diperketat

`mimes:jpg,jpeg,png,pdf|max:5120` sudah memvalidasi berdasarkan MIME asli (bukan hanya ekstensi nama), sehingga lumayan. Setelah pindah ke disk privat (#2), risiko konten berbahaya sudah minim. Bila ingin lebih ketat, pakai:

```php
'mimetypes:image/jpeg,image/png,application/pdf'
```

---

## Yang Sudah Aman ✅

- Query pakai Eloquent → aman SQL injection.
- CSRF aktif (middleware `web`), validasi input komprehensif per jenis surat.
- File fields di-exclude dari `data_permohonan` JSON.
- Whitelist status pada edit/update — mencegah edit surat yang sudah final.
- Kegagalan WhatsApp tidak membatalkan transaksi (try-catch + Log::error).

---

## Referensi File

- `app/Http/Controllers/Layanan/PermohonanController.php` — form publik, upload, revisi, OCR
- `app/Http/Controllers/Admin/Surat/PermohonanSuratController.php` — detail, download, approve/reject, destroy
- `app/Http/Controllers/Layanan/TrackingController.php` — cek status publik
- `app/Http/Requests/StorePermohonanRequest.php` — aturan validasi
- `app/Services/PermohonanSuratService.php` — `getPermohonanById`, `deletePermohonan`
- `app/Repositories/PermohonanSuratRepository.php` — `getByUserRole` (scoping list)
- `config/filesystems.php` — konfigurasi disk `public`
- `routes/portal.php` — route publik
