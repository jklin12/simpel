# Security Review — Full Branch (`feat/security-patch`)

**Tanggal:** 2026-07-02
**Cakupan:** Seluruh branch `feat/security-patch` dibanding `main` (295 file) — routing, controller, service, template Blade, middleware, config.
**Metode:** Manual static code review. **Tidak ada** pengujian dinamis/aktif. Tooling SAST (semgrep/gitleaks) tidak tersedia di environment ini.
**Pelengkap:** [security-review-permohonan-surat.md](security-review-permohonan-surat.md) (review sebelumnya, khusus alur permohonan pada `release/v1.0.7`).

---

## Ringkasan Eksekutif

Fondasi keamanan tetap baik: seluruh query pakai Eloquent (aman SQL injection), CSRF aktif di grup `web`, dan `DataKelurahanController` sudah men-scope data per kelurahan dengan benar.

Namun review menyeluruh menemukan **4 isu baru** yang belum tercatat di review sebelumnya (ditandai 🆕), dan **memastikan seluruh temuan review lama masih hidup** (belum ada perbaikan yang diterapkan di branch ini).

**Prioritas tindakan:** (1) Perbaiki otorisasi/IDOR permohonan → (2) throttle login admin → (3) pindahkan dokumen PII ke disk privat.

| # | Severity | Temuan | Status |
|---|----------|--------|--------|
| 1 | 🔴 CRITICAL | Broken access control / IDOR di seluruh endpoint admin permohonan | masih hidup, lebih luas dari doc lama |
| 2 | 🟠 HIGH | Tidak ada rate limit di login admin → brute-force kredensial | 🆕 |
| 3 | 🟠 HIGH | Dokumen PII + surat TTD tersimpan di disk `public` (dapat diakses tanpa login) | masih hidup |
| 4 | 🟡 MEDIUM | Tidak ada throttle di endpoint publik (`/ocr` cost-DoS, `store`, `revisi`, tracking) | masih hidup |
| 5 | 🟡 MEDIUM | Stored XSS via `{!! $berita->konten !!}` yang tidak di-escape | 🆕 |
| 6 | 🟡 MEDIUM | `dd($e)` tertinggal di jalur PDF produksi → bocor stack trace | 🆕 |
| 7 | 🔵 LOW | Tracking token mengembalikan seluruh PII termasuk NIK penuh | masih hidup |
| 8 | 🔵 LOW | Pesan `$e->getMessage()` mentah ke user; mass-assignment `$request->all()` di KabupatenController | masih hidup + 🆕 |
| 9 | 🔵 LOW | File operasional/PII ter-commit ke git (`storage/Data input admin kelurahan.pdf`) | 🆕 |

---

## 🔴 CRITICAL 1 — IDOR / Broken Access Control pada admin permohonan

Route di `routes/admin.php:74-95` hanya dijaga middleware **`auth` saja — tanpa middleware `role` dan tanpa cek kepemilikan wilayah**:

```php
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('permohonan-surat/{permohonanSurat}', ...->name('permohonan-surat.show'));
    Route::put('permohonan-surat/{permohonanSurat}', ...->name('permohonan-surat.update'));
    Route::get('permohonan-surat/{permohonanSurat}/dokumen/{dokumen}/download', ...);
    Route::delete('permohonan-surat/{permohonanSurat}', ...->name('permohonan-surat.destroy'));
    ...
});
```

Service tidak melakukan scoping apa pun — `app/Services/PermohonanSuratService.php:51`:

```php
public function getPermohonanById($id)
{
    return $this->repository->find($id);   // tidak ada cek kelurahan/kecamatan
}
```

**Dampak:** Setiap user terautentikasi cukup mengganti angka ID pada URL untuk:
- **Membaca** detail & mengunduh KTP/KK/buku nikah/surat TTD warga di kelurahan mana pun (`show`, `downloadDokumen`, `downloadLetter`).
- **Mengubah** data (`update` — menimpa NIK/nama/alamat/`data_permohonan`).
- **Menghapus** permohonan (`destroy`).

`approve`/`reject` sebagian terlindungi oleh cek role per-step di service, tetapi `show/edit/update/downloadLetter/downloadDokumen` **tidak**. Sebagai perbandingan, `app/Http/Controllers/Admin/Portal/DataKelurahanController.php:110-152` **sudah** men-scope dengan benar — jadi ini inkonsistensi/kelalaian, bukan ketidaktahuan pola.

Contoh:
```
/admin/permohonan-surat/1523/dokumen/8801/download   ← ganti ID, tetap tembus
```

### Perbaikan
Buat `PermohonanPolicy` (`view`/`update`/`delete` di-scope `kelurahan_id`/`kecamatan_id`) lalu panggil `$this->authorize('view', $permohonan)` di tiap method controller. Alternatif cepat: pusatkan cek kepemilikan di dalam `getPermohonanById()`:

```php
public function getPermohonanById($id)
{
    $permohonan = $this->repository->find($id);
    $user = auth()->user();

    if ($user->hasRole('admin_kelurahan')
        && $permohonan->kelurahan_id !== $user->kelurahan_id) {
        abort(403);
    }
    if ($user->hasRole('admin_kecamatan')
        && optional($permohonan->kelurahan)->kecamatan_id !== $user->kecamatan_id) {
        abort(403);
    }
    return $permohonan;
}
```

---

## 🟠 HIGH 2 — Login admin tanpa rate limiting (brute-force) 🆕

`app/Http/Controllers/Auth/LoginController.php:23-34` memanggil `Auth::attempt` langsung **tanpa `RateLimiter`**:

```php
public function store(Request $request)
{
    $credentials = $request->validate([...]);
    if (! Auth::attempt($credentials, $request->boolean('remember'))) {
        throw ValidationException::withMessages(['email' => trans('auth.failed')]);
    }
    ...
}
```

Route login (`routes/admin.php:18`) **tidak** memakai `throttle`, dan grup `web` di `app/Http/Kernel.php:32-39` juga tidak punya throttle. Artinya **percobaan password ke panel admin tidak terbatas**.

### Perbaikan
Pakai pola bawaan Laravel (`LoginRequest::authenticate()` dengan `RateLimiter`), atau minimal:
```php
Route::post('login', [LoginController::class, 'store'])->middleware('throttle:5,1');
```
Idealnya rate limit di-key berdasarkan `email + IP` dan lakukan `RateLimiter::hit()` pada tiap kegagalan.

---

## 🟠 HIGH 3 — Dokumen PII & surat TTD di disk `public`

`app/Http/Controllers/Layanan/PermohonanController.php:253` menyimpan KTP/KK/buku nikah ke disk `public`:

```php
$path = $file->store('permohonan/' . $permohonan->id . '/dokumen', 'public');
```

Hal serupa pada revisi (`handleRevisiFileUploads:402`) dan **surat final yang sudah ditandatangani** — `app/Http/Controllers/Admin/Surat/PermohonanSuratController.php:247`:

```php
$path = $request->file('signed_letter')->store('surat-selesai/' . $id, 'public');
```

Semua berkas ini (KTP, KK, buku nikah, dan surat jadi berisi nama+NIK+alamat) dapat diunduh **siapa saja tanpa autentikasi** lewat URL `/storage/...`. Nama file acak **bukan kontrol akses** (bocor lewat history/Referer/log/share → tembus permanen).

### Perbaikan
1. Ganti ke disk privat (`local`) pada semua upload store & revisi, serta signed letter.
2. Sajikan file **hanya** lewat controller yang sudah di-scope otorisasi (lihat #1).
3. Migrasikan file lama dari `storage/app/public/...` ke `storage/app/...`.
4. Sesuaikan `downloadDokumen`, `downloadSignedLetter`, dan base64 pas foto SPN (`PermohonanSuratController.php:169`) untuk membaca dari disk privat.

---

## 🟡 MEDIUM 4 — Endpoint publik tanpa throttle

`routes/portal.php:38-48` tidak ada satupun yang di-throttle. Grup `web` juga tidak punya throttle global.

- **`/ocr`** (`ocrKtp`, `PermohonanController.php:420`) — tanpa login, **memanggil Anthropic API berbayar**. Rawan cost-DoS: spam request menguras kuota & tagihan. **Paling mendesak.**
- **`/ajukan` (store)** — bisa dibanjiri permohonan sampah.
- **`/revisi/{track_token}` (update)** — endpoint tak-terautentikasi yang memodifikasi data.
- **`/cek-status/search`** — brute-force / enumerasi `track_token`.

### Perbaikan
```php
Route::post('/ocr', [PermohonanController::class, 'ocrKtp'])->middleware('throttle:10,1')->name('ocr');
Route::post('/ajukan', [PermohonanController::class, 'store'])->middleware('throttle:20,1')->name('store');
Route::post('/revisi/{track_token}', [PermohonanController::class, 'update'])->middleware('throttle:20,1')->name('revisi.update');
Route::get('/cek-status/search', [TrackingController::class, 'search'])->middleware('throttle:30,1')->name('tracking.search');
```

---

## 🟡 MEDIUM 5 — Stored XSS pada konten berita 🆕

`resources/views/portal/berita/show.blade.php:32` merender HTML mentah ke setiap pengunjung publik:

```blade
{!! $berita->konten !!}
```

Konten ditulis oleh `admin_kecamatan`/`super_admin`, tetapi itu role multi-akun dengan tingkat kepercayaan lebih rendah — akun kecamatan yang jahat atau ter-phishing dapat menyuntikkan JS yang tersaji ke seluruh pengunjung situs. (View lain — `index`, `peta`, `faq`, `struktur-node` — sudah benar memakai `strip_tags`/`e()`.)

### Perbaikan
Sanitasi HTML saat disimpan memakai HTML purifier (mis. `mews/purifier`), lalu render nilai yang sudah dibersihkan. Jangan pernah `{!! !!}` untuk input user tanpa purifikasi.

---

## 🟡 MEDIUM 6 — `dd($e)` tertinggal di jalur produksi 🆕

`app/Http/Controllers/Admin/Surat/PermohonanSuratController.php:219` (`downloadLetter`):

```php
} catch (\Exception $e) {
    dd($e);   // ← dump stack trace penuh ke browser
    return redirect()->back()->with('error', 'Gagal generate PDF: ' . $e->getMessage());
}
```

Setiap exception saat generate surat akan men-dump **stack trace lengkap** (path, SQL ter-bind, potongan config) ke browser, dan membuat redirect graceful di bawahnya menjadi dead code.

### Perbaikan
Hapus `dd($e)`, cukup `Log::error($e)` + pesan generik ke user.

---

## 🔵 LOW 7 — Tracking token membuka seluruh PII

`app/Http/Controllers/Layanan/TrackingController.php:17` mengembalikan data lengkap (nama, **NIK penuh**, alamat, no WA, seluruh `data_permohonan`) untuk token valid apa pun. Entropi `Str::random(10)` uppercase besar, tapi tanpa throttle (#4) tetap layak diperkuat. Pertimbangkan **masking NIK** di `resources/views/user/permohonan/track.blade.php` (mis. `3271••••••••1234`).

---

## 🔵 LOW 8 — Kebocoran pesan error & mass-assignment

- `$e->getMessage()` mentah ditampilkan ke user: `PermohonanController.php:229`, `:371`, `:561`. Berpotensi membocorkan detail SQL/path. Sebaiknya `Log::error` untuk detail + pesan generik.
- `app/Http/Controllers/Admin/Master/KabupatenController.php:45` & `:68` memakai `$request->all()` untuk `create`/`update` (mass assignment). Hanya super_admin, jadi risiko rendah, tetapi ganti ke `$request->validated()`. 🆕

---

## 🔵 LOW 9 — File sensitif ter-commit ke git 🆕

File berikut ter-track di repo:
- `storage/Data input admin kelurahan.pdf`
- `storage/list pekerjaan.txt`
- `storage/template_SURAT KETERANGAN MENIKAH.pdf`

Berkas ini di `storage/` (bukan `public/`) sehingga tidak web-accessible, tetapi ada di dalam repo. `Data input admin kelurahan.pdf` berpotensi berisi data warga asli — verifikasi isinya; jika benar berisi PII, `git rm` dan tambahkan ke `.gitignore`.

---

## Yang Sudah Aman ✅

- Semua query pakai Eloquent → aman SQL injection.
- CSRF aktif di grup `web`.
- `DataKelurahanController` sudah men-scope data per kelurahan dengan benar (`isAdminKelurahan()` + cek `kelurahan_id`).
- Download template surat pakai route-model binding (`TemplateSurat $template`) → tidak ada path traversal dari parameter.
- CORS wildcard (`allowed_origins: ['*']`) hanya berlaku untuk `api/*` dengan `supports_credentials: false` → risiko rendah.
- Kegagalan notifikasi WhatsApp di-swallow (try-catch + `Log::error`) tanpa membatalkan transaksi.
- File fields di-exclude dari `data_permohonan` JSON.
- Whitelist status pada edit/update — mencegah edit surat yang sudah final.

---

## Referensi File

- `routes/admin.php` — route admin (auth-only, tanpa scoping) — **#1**
- `routes/portal.php` — route publik tanpa throttle — **#4**
- `app/Http/Kernel.php` — grup `web` tanpa throttle
- `app/Http/Controllers/Auth/LoginController.php` — login tanpa rate limit — **#2**
- `app/Services/PermohonanSuratService.php` — `getPermohonanById` tanpa scoping — **#1**
- `app/Http/Controllers/Admin/Surat/PermohonanSuratController.php` — `dd($e)`, disk public, IDOR — **#1, #3, #6**
- `app/Http/Controllers/Layanan/PermohonanController.php` — upload disk public, OCR, error mentah — **#3, #4, #8**
- `app/Http/Controllers/Layanan/TrackingController.php` — PII via track_token — **#7**
- `resources/views/portal/berita/show.blade.php` — XSS `{!! !!}` — **#5**
- `app/Http/Controllers/Admin/Master/KabupatenController.php` — mass assignment — **#8**
- `app/Http/Controllers/Admin/Portal/DataKelurahanController.php` — contoh scoping yang benar ✅
