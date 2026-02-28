---
description: Cara menambah jenis surat baru di aplikasi SIMPEL (semi-manual & dynamic)
---

# Panduan Menambah Jenis Surat Baru di SIMPEL

Ada **dua pendekatan** yang bisa dikombinasikan:

| Mode | Kapan dipakai | Form template |
|------|--------------|---------------|
| **Dynamic** | Form sederhana, field standar (text, file, date, select) | `dynamic.blade.php` (auto) |
| **Semi-manual** | Form kompleks, OCR, logika khusus, custom UI | File `.blade.php` sendiri |

---

## OPTION A — Fully Dynamic (tanpa file baru)

Cocok untuk jenis surat dengan form standar. Tidak perlu membuat file apapun.

### 1. Tambah record di admin

Masuk ke **Admin → Jenis Surat → Tambah**, isi:
- **Kode**: huruf kapital, contoh `SKPD`
- **Nama**: nama lengkap surat
- **Template**: pilih `dynamic` (atau kosongkan jika ada fallback)
- **Field Permohonan**: tambah field-field yang diperlukan (lihat tipe yang tersedia di bawah)
- **Petunjuk Lampiran**: upload foto contoh dan isi keterangan per attachment field

### Tipe field yang tersedia di dynamic:
| Tipe | Kegunaan |
|------|----------|
| `text` | Input teks biasa |
| `textarea` | Teks panjang / keterangan |
| `date` | Tanggal (dengan date picker) |
| `number` | Angka |
| `select` | Dropdown pilihan (isi opsi dipisah koma) |
| `file` | Upload dokumen (PDF/gambar) |

### 2. Pastikan `form_type` model mengarah ke `dynamic`

```php
// Di database: kolom form_type = 'dynamic'
// Atau lihat di model JenisSurat::getFormType()
```

---

## OPTION B — Semi-Manual (kombinasi template + dynamic)

Cocok untuk surat dengan form kompleks (OCR KTP, field kondisional, UI khusus).

### Langkah 1 — Buat file form template

```
resources/views/user/permohonan/types/{kode_surat_lowercase}.blade.php
```

Contoh: untuk SKTM → `sktm.blade.php`

Gunakan template ini sebagai starting point:

```blade
{{-- resources/views/user/permohonan/types/nama_surat.blade.php --}}
{{-- Variabel tersedia: $service (JenisSurat), $user, old() --}}

{{-- ═══ BAGIAN 1: DATA PEMBUAT SURAT ═══ --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 mb-8">
    <div class="flex items-center gap-3 mb-6">
        <span class="w-8 h-8 rounded-full bg-primary-600 text-white text-sm flex items-center justify-center font-bold">1</span>
        <h2 class="text-lg font-bold text-gray-900">Data Permohonan</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Contoh field teks --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Nama Lengkap <span class="text-red-500">*</span>
            </label>
            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required
                class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4">
            @error('nama_lengkap') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Field OCR KTP (jika butuh) --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                NIK <span class="text-red-500">*</span>
            </label>
            <input type="text" name="nik" value="{{ old('nik') }}" required maxlength="16"
                class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4">
            @error('nik') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

    </div>
</div>

{{-- ═══ BAGIAN 2: BERKAS LAMPIRAN ═══ --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 mb-8">
    <div class="flex items-center gap-3 mb-6">
        <span class="w-8 h-8 rounded-full bg-primary-600 text-white text-sm flex items-center justify-center font-bold">2</span>
        <h2 class="text-lg font-bold text-gray-900">Berkas Pendukung</h2>
    </div>
    <p class="text-sm text-gray-500 mb-6">Format yang diterima: JPG, PNG, PDF. Maksimal 5 MB per file.</p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Contoh file upload --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                KTP Pemohon <span class="text-red-500">*</span>
            </label>
            <input type="file" name="ktp_pemohon" accept=".jpg,.jpeg,.png,.pdf" required
                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
            @error('ktp_pemohon') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

    </div>
</div>
```

### Langkah 2 — Daftarkan record di database via Admin

Masuk ke **Admin → Jenis Surat → Tambah**, isi:
- **Kode**: harus SAMA dengan nama file (tanpa ekstensi, huruf kapital)
  - Contoh: file `sktm.blade.php` → kode `SKTM`
- **Form Type**: isi `sktm` (atau nama file-nya, lowercase)
- **Required Fields**: isi nama-nama field yang wajib divalidasi (nama field = atribut `name` di input HTML)
- **Petunjuk Lampiran**: upload contoh file dan keterangan per field

### Langkah 3 — Tambahkan validasi di request

Buka `app/Http/Requests/StorePermohonanSuratRequest.php` dan cari switch/match form_type:

```php
// Tambahkan case untuk form type baru
case 'nama_surat':
    return [
        'nama_lengkap' => 'required|string|max:255',
        'nik'          => 'required|digits:16',
        'ktp_pemohon'  => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        // ... field lainnya
    ];
```

### Langkah 4 — Tambahkan template surat PDF (opsional, jika perlu cetak)

Buka `app/Services/Surat/` dan tambahkan service handler untuk generate PDF-nya.

---

## File-file yang TIDAK perlu diubah

Jika mengikuti alur yang benar, file berikut **TIDAK perlu disentuh**:

- `app/Http/Controllers/*/PermohonanSuratController.php` (sudah generic)
- `resources/views/user/permohonan/create_public.blade.php` (sudah include dynamic)
- `resources/views/user/permohonan/revisi.blade.php` (sudah generic)
- Database migration (sudah ada kolom yang diperlukan)

---

## Checklist Tambah Jenis Surat Baru

### Dynamic (tanpa file baru)
- [ ] Tambah record di Admin → Jenis Surat
- [ ] Isi Field Permohonan (tipe + label + required)
- [ ] Isi Petunjuk Lampiran (keterangan + upload contoh file)
- [ ] Test pengajuan dari halaman layanan

### Semi-manual (dengan file baru)
- [ ] Buat file `resources/views/user/permohonan/types/{kode}.blade.php`
- [ ] Tambah record di Admin → Jenis Surat (kode = nama file lowercase)
- [ ] Isi `required_fields` di admin dengan nama-nama input field
- [ ] Tambah case validasi di `StorePermohonanSuratRequest`
- [ ] Isi Petunjuk Lampiran
- [ ] Test pengajuan dan revisi
- [ ] (Opsional) Tambah template PDF jika butuh cetak surat

---

## Struktur field `required_fields` di database

Format baru (object per field, untuk dynamic builder):
```json
[
  {"name": "nama_lengkap", "label": "Nama Lengkap", "type": "text", "is_required": true, "options": null},
  {"name": "jenis_kelamin", "label": "Jenis Kelamin", "type": "select", "is_required": true, "options": ["Laki-laki","Perempuan"]},
  {"name": "ktp_pemohon", "label": "Scan KTP", "type": "file", "is_required": true, "options": null}
]
```

Format lama (hanya nama field, untuk hardcoded forms):
```json
["nama_lengkap", "nik", "ktp_pemohon"]
```

> **Catatan:** Kedua format didukung oleh sistem. Format lama akan auto-dikenali saat edit di admin.
