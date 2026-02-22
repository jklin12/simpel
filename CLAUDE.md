# CLAUDE.md — Simpel Landasan Ulin

Sistem Pelayanan Elektronik Kelurahan Landasan Ulin. Laravel 10 + MySQL + TailwindCSS.

---

## Stack & Dependencies

- **Framework:** Laravel 10 (PHP 8.1+)
- **Frontend:** TailwindCSS, Alpine.js
- **PDF:** `barryvdh/laravel-dompdf` — di-stream (bukan download)
- **QR Code:** `simplesoftwareio/simple-qrcode` — embed base64 PNG ke PDF
- **Roles:** `spatie/laravel-permission`
- **Database:** MySQL (Laragon lokal) — tabel master pakai prefix `m_` (contoh: `m_kelurahans`, `m_kecamatans`)

---

## Arsitektur

Pattern: **Controller → Service → Repository → Model**

```
app/
├── Http/Controllers/
│   ├── Admin/                  # Semua controller admin
│   │   ├── PermohonanSuratController.php   # termasuk downloadLetter (PDF stream)
│   │   ├── JenisSuratController.php
│   │   └── ...
│   ├── PublicPermohonanController.php      # Form publik + file upload
│   ├── KelurahanController.php
│   └── TrackingController.php             # Cek status by track_token
├── Services/
│   ├── PermohonanSuratService.php         # generateNomorSurat ada di sini
│   ├── JenisSuratService.php              # transform required_fields
│   ├── ApprovalFlowService.php
│   ├── SuratCounterService.php
│   └── UserService.php
└── Repositories/
    ├── Contracts/              # Interface untuk tiap repository
    ├── PermohonanSuratRepository.php
    ├── JenisSuratRepository.php
    └── ...
```

---

## Key Models & Kolom Penting

### `JenisSurat` (tabel: `jenis_surats`)
- `kode` — uppercase, unik (SKTM, SKTMR, SKM, dll.)
- `required_fields` — JSON array of `{name, label, type, is_required, options}`, di-cast ke `array`
- `is_active` — boolean

**Tipe field yang didukung di `required_fields`:** `text`, `textarea`, `date`, `number`, `select`, `file`

### `Kelurahan` (tabel: `m_kelurahans`)
- `kode` — kode Kemendagri
- `akronim` — digunakan untuk **penomoran surat** (contoh: LU → `001/SKTM/LU/II/2026`)
- `lurah_nama`, `lurah_nip`, `lurah_pangkat`, `lurah_golongan`
- `kop_surat_path` — path di public storage, dipakai sebagai header PDF
- `kecamatan_id` — Kecamatan Landasan Ulin = `6372010`

### `PermohonanSurat` (tabel: `permohonan_surats`)
- `track_token` — 10 karakter uppercase random, untuk cek status publik
- `nomor_permohonan` — format `REG/YYYYMMDD/XXXXX`
- `nomor_surat` — di-generate oleh `PermohonanSuratService::generateNomorSurat()` saat approve
- `data_permohonan` — JSON, cast ke `array`, berisi semua field form kecuali file
- `status` — `pending`, `approved`, `rejected`, dll.
- Relasi: `jenisSurat()`, `kelurahan()`, `dokumens()` (HasMany PermohonanDokumen)

---

## Penomoran Surat

Logika ada di `PermohonanSuratService::generateNomorSurat()`.

Format menggunakan **`$kelurahan->akronim`** dengan fallback ke `kode` lalu `nama`:
```php
$kodeKelurahan = strtoupper($kelurahan->akronim ?? $kelurahan->kode ?? $kelurahan->nama);
```

- SKTMR: `600.2/001/II/LU/2026`
- Lainnya: `001/SKTM/LU/II/2026`

---

## Form Permohonan: Static vs Dynamic

### Jenis surat yang pakai template STATIS (jangan diubah ke dynamic):
| Kode | Form template | PDF template | Validation |
|------|--------------|--------------|------------|
| SKTM | `types/sktm.blade.php` | `pdf/sktm.blade.php` | `getSktmRules()` |
| SKTMR | `types/sktmr.blade.php` | `pdf/sktmr.blade.php` | `getSktmrRules()` |
| SKM | `types/skm.blade.php` | *(belum ada PDF)* | `getSkmRules()` |

Form publik di `create_public.blade.php` memakai logika:
```blade
@if(View::exists('user.permohonan.types.' . strtolower($service->kode)))
    {{-- template statis --}}
@elseif($service->required_fields && count($service->required_fields) > 0)
    @include('user.permohonan.types.dynamic', ...)   {{-- dynamic fallback --}}
@endif
```

### Jenis surat BARU → pakai sistem dynamic:
- Admin definisi field via UI di admin/jenis-surat/create atau edit
- Form publik render otomatis dari `required_fields`
- Validasi di `StorePermohonanRequest` switch `default:` generate rules dari `required_fields`
- File upload di `PublicPermohonanController::handleFileUploads()` merge dynamic file fields

---

## PDF Generation

File: `app/Http/Controllers/Admin/PermohonanSuratController.php` → `downloadLetter()`

- Library: DomPDF (`\Barryvdh\DomPDF\Facade\Pdf`)
- Output: **`->stream($filename)`** (dibuka di browser, bukan download)
- Font: **Arial** (bukan Times New Roman)
- Warna teks: **hitam semua** (#000) — tidak ada highlight berwarna
- QR code: di-generate via `simplesoftwareio/simple-qrcode`, embed sebagai base64 PNG
- QR link ke: `route('tracking.search', ['track_token' => ...])`
- QR posisi: dalam kolom TTD, **setelah spacer (ruang TTD), sebelum nama lurah**

Template PDF tersedia untuk:
- `resources/views/pdf/sktm.blade.php`
- `resources/views/pdf/sktmr.blade.php`

Variabel yang dipass ke PDF view: `$permohonan`, `$kelurahan`, `$lurah`, `$qrBase64`

---

## File Upload

- Disk: `public`
- Path: `permohonan/{id}/dokumen/`
- Tipe diterima: `jpg`, `jpeg`, `png`, `pdf` — maks 5MB
- File field di-exclude dari `data_permohonan` JSON (lihat `StorePermohonanRequest::fileFields()`)
- Dynamic file fields juga di-exclude di `PublicPermohonanController::store()`

---

## Notifikasi

- **WhatsApp** (`PermohonanCreatedWhatsapp`) — dikirim ke pemohon saat permohonan dibuat
- **Database notification** (`PermohonanBaruNotification`) — dikirim ke admin_kelurahan
- Kegagalan WA di-swallow dengan try-catch + `Log::error` — **tidak membatalkan transaksi**

---

## Testing

- Framework: **PHPUnit** (bukan Pest)
- Database: **MySQL langsung** (SQLite dinonaktifkan di `phpunit.xml`)
- Trait yang dipakai: **`DatabaseTransactions`** — data di-rollback setelah tiap test, DB tidak kotor
- **Jangan pakai `RefreshDatabase`** — akan drop semua tabel termasuk seed data
- Storage di test: `Storage::fake('public')`
- Test files: `tests/Feature/`

```bash
php artisan test tests/Feature/NamaTest.php
```

---

## Deployment

Lihat `DEPLOYMENT.md` untuk detail lengkap.

Queue worker via Supervisor:
```bash
sudo supervisorctl restart simpel-queue:*
```

---

## Konvensi Penting

- **Kode jenis surat selalu UPPERCASE** — di-enforce di `JenisSuratService`
- **`akronim` kelurahan** wajib diisi untuk penomoran surat yang benar
- **Pangkat/Golongan lurah** muncul di PDF antara nama dan NIP (conditional, hanya jika diisi)
- Jangan sentuh template statis SKTM/SKTMR/SKM untuk perubahan sistem — edit file Blade/PHP secara manual jika perlu update field
- Model `Kelurahan` menggunakan `$guarded = ['id']` — semua field mass-assignable
