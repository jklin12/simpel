---
description: Cara menambah template surat baru di aplikasi SIMPEL
---

# Menambah Template Surat Baru

Setiap surat baru membutuhkan **5 komponen** yang harus dibuat secara berurutan:

1. Form pengajuan publik (`blade`)
2. Validasi input & upload
3. PDF template
4. Seeder data (jenis_surat + approval_flow)
5. Seeder label dokumen

---

## Langkah 1 — Tentukan Kode Surat

Pilih kode unik HURUF KAPITAL, contoh: `SKPINDAH`, `SKN`, `SKKL`.

> Kode ini menentukan nama file yang dipakai di semua langkah berikutnya.

---

## Langkah 2 — Buat Form Pengajuan (Blade Partial)

Buat file: `resources/views/user/permohonan/types/{kode_lowercase}.blade.php`

Contoh untuk kode `SKPINDAH` → file: `types/skpindah.blade.php`

Template dasar yang bisa dicopy dari `types/sktmr.blade.php`, lalu sesuaikan:

- Nama Alpine.js handler function (contoh: `ocrSkpindahHandler`)
- Input fields sesuai kebutuhan surat
- Nama field file upload (prefix dengan kode surat lowercase, contoh: `skpindah_ktp_kk`)
- Nomor section (Seksi 1, 2, 3, dst.)

**Field wajib yang selalu ada di semua surat:**
- Bagian OCR KTP → nama, NIK, JK, Agama, TTL, Alamat
- Surat Pengantar RT/RW → RT, RW, No. Surat, Tanggal
- Upload berkas lampiran

---

## Langkah 3 — Tambah Validasi Input & File

Edit `app/Http/Requests/StorePermohonanRequest.php`:

### a) Tambah case di method `rules()`:
```php
case 'SKPINDAH':
    $specificRules = $this->getSkpindahRules();
    break;
```

### b) Buat method private `getSkpindahRules()`:
```php
private function getSkpindahRules()
{
    return [
        'nama_lengkap'   => 'required|string|max:255',
        'nik_bersangkutan' => 'required|string|size:16',
        // ... field lainnya
        // File upload:
        'skpindah_surat_pengantar' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        'skpindah_ktp_kk'          => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
    ];
}
```

### c) Tambah file fields di method `fileFields()`:
```php
'skpindah_surat_pengantar',
'skpindah_ktp_kk',
// ... field file lainnya
```

---

## Langkah 4 — Tambah Label Dokumen

Edit `app/Models/PermohonanDokumen.php`, tambah ke array `JENIS_DOKUMEN`:

```php
// SKPINDAH
'skpindah_surat_pengantar' => 'Surat Pengantar RT/RW Setempat',
'skpindah_ktp_kk'          => 'KTP & KK yang Bersangkutan',
```

---

## Langkah 5 — Buat PDF Template

Buat file: `resources/views/pdf/{kode_lowercase}.blade.php`

Contoh: `resources/views/pdf/skpindah.blade.php`

Copy dari `pdf/sktmr.blade.php` lalu sesuaikan:
- `<title>` dan `.surat-title` → judul surat
- Data pemohon di `<table class="data-table">` → sesuai field
- Narasi paragraf → teks keterangan yang relevan
- Penutup → kalimat penutup surat

**Variabel yang tersedia di PDF template:**
```php
$permohonan         // Model PermohonanSurat
$permohonan->data_permohonan  // array data form (JSON di DB)
$kelurahan          // Model Kelurahan (dengan relasi kecamatan)
$lurah              // array ['nama' => ..., 'nip' => ...]
```

**Cara akses data form:**
```php
@php
$data = $permohonan->data_permohonan ?? [];
@endphp

{{ $data['nama_lengkap'] ?? '-' }}
{{ $data['alamat_lengkap'] ?? '-' }}
```

---

## Langkah 6 — Buat Seeder

Buat file: `database/seeders/{KodeSurat}Seeder.php`

Contoh: `SkpindahSeeder.php`

```php
<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisSurat;
use App\Models\ApprovalFlow;
use App\Models\Kelurahan;

class SkpindahSeeder extends Seeder
{
    public function run(): void
    {
        $surat = JenisSurat::updateOrCreate(
            ['kode' => 'SKPINDAH'],
            [
                'nama'      => 'Surat Keterangan Pindah',
                'deskripsi' => 'Deskripsi singkat surat.',
                'required_fields' => json_encode([
                    'nama_lengkap', 'nik_bersangkutan', ...
                    'skpindah_surat_pengantar', 'skpindah_ktp_kk',
                ]),
                'is_active' => true,
            ]
        );

        $kelurahans = Kelurahan::where('kecamatan_id', '6372010')->get();

        foreach ($kelurahans as $kelurahan) {
            $flow = ApprovalFlow::updateOrCreate(
                ['jenis_surat_id' => $surat->id, 'kelurahan_id' => $kelurahan->id],
                [
                    'nama'                       => 'Flow Kelurahan SKPINDAH',
                    'require_kecamatan_approval' => false, // true jika butuh kecamatan
                    'require_kabupaten_approval' => false,
                    'is_active'                  => true,
                ]
            );

            $flow->steps()->delete();

            $flow->steps()->create([
                'step_order'  => 1,
                'role_name'   => 'admin_kelurahan',
                'step_name'   => 'Verifikasi Berkas oleh Admin Kelurahan',
                'is_required' => true,
            ]);
        }
    }
}
```

// turbo
Jalankan seeder:
```
php artisan db:seed --class=SkpindahSeeder
```

---

## Langkah 7 — Update JenisSuratSeeder (opsional)

Edit `database/seeders/JenisSuratSeeder.php` tambahkan entry surat baru ke array `$jenisSurats` agar ikut saat full re-seed.

---

## Catatan Penting

### Format Nomor Surat

Edit `app/Services/PermohonanSuratService.php` method `generateNomorSurat()`:

- **Format SKTMR** (sudah ada): `600.2/002/I/KEL.SN/2026`
- **Format default lainnya**: `001/KODEJENIS/KEL/I/2026`

Jika surat baru punya format khusus, tambahkan kondisi `if ($kodeJenis === 'SKPINDAH') { ... }` di method tersebut.

### Nomor Romawi Bulan

Helper `toRoman()` sudah tersedia di `PermohonanSuratService`, tidak perlu dibuat ulang.

### Approval Flow Multi-Step (Kecamatan)

Jika surat memerlukan persetujuan kecamatan, set `require_kecamatan_approval => true` dan tambahkan step ke-2:

```php
$flow->steps()->create([
    'step_order'  => 2,
    'role_name'   => 'admin_kecamatan',
    'step_name'   => 'Verifikasi Admin Kecamatan',
    'is_required' => true,
]);
```

---

## Checklist Ringkas

```
[ ] types/{kode}.blade.php        — Form pengajuan publik
[ ] StorePermohonanRequest.php    — Tambah case + method rules + fileFields
[ ] PermohonanDokumen.php         — Tambah label JENIS_DOKUMEN
[ ] pdf/{kode}.blade.php          — Template PDF surat
[ ] {Kode}Seeder.php              — Seeder jenis_surat + approval_flow
[ ] JenisSuratSeeder.php          — Tambah entry (opsional, untuk full reseed)
[ ] PermohonanSuratService.php    — Tambah format nomor surat (jika format khusus)
```
