# Mockup UI Mobile: Beranda, Struktur Organisasi, Daftar Layanan, Download Template

Mockup HTML statis (Tailwind CDN + font Outfit, gaya sama dengan `layouts/landing.blade.php`) untuk revamp mobile-first empat halaman publik. **Ini mockup, bukan kode produksi**: tidak ada Blade, tidak ada data nyata, dan tidak ada perubahan pada aplikasi.

Dibuat sebagai pengganti prototipe Figma yang terhenti karena kuota Figma MCP plan Starter (lihat [../revamp-halaman-publik-mobile.md](../revamp-halaman-publik-mobile.md)). Mockup ini bisa langsung dijadikan acuan Blade karena memakai kelas Tailwind yang sama.

## Cara melihat

- Buka `index.html` di browser (galeri 6 layar dalam bingkai HP), atau buka tiap halaman langsung. Perlu internet (Tailwind CDN dan Google Fonts).
- Preview PNG (lebar 375px, skala 2x) ada di [previews/](previews/):

| Layar | File | Tinggi halaman |
|---|---|---|
| Beranda | `previews/beranda.png` | 1.850px |
| Beranda, menu terbuka | `previews/beranda-menu.png` | 812px |
| Struktur organisasi | `previews/struktur-organisasi.png` | 1.458px |
| Daftar layanan | `previews/daftar-layanan.png` | 2.712px |
| Daftar layanan, pilih kelurahan | `previews/daftar-layanan-sheet.png` | 812px |
| Download template | `previews/download-template.png` | 1.948px |

## Perbandingan dengan produksi (375px)

Tinggi Before diukur dari `scrollHeight` produksi `https://simpel-bjb.id/` pada 23 November 2026 (lihat dokumen progres). Tinggi After dari mockup ini. Mockup memakai data contoh, jadi angka After akan berubah mengikuti data asli (terutama struktur organisasi, yang di sini hanya berisi beberapa nama placeholder).

| Halaman | Before | After (mockup) | Perubahan |
|---|---|---|---|
| Beranda | sekitar 3.627px | 1.850px | sekitar 49% lebih pendek |
| Struktur organisasi | sekitar 2.462px (plus scroll horizontal) | 1.458px, tanpa scroll horizontal | sekitar 41% lebih pendek |
| Daftar layanan | sekitar 8.820px | 2.712px | sekitar 69% lebih pendek |
| Download template | sekitar 4.379px | 1.948px | sekitar 56% lebih pendek |

## Ringkasan desain

Pola umum: navbar 56px yang menempel (logo dan hamburger, drawer berisi semua menu termasuk FAQ dan Cek Status), tombol dan baris minimal 44-48px, footer berupa accordion, tanpa konten yang memaksa scroll horizontal.

| Halaman | File | Perubahan utama |
|---|---|---|
| Beranda | `beranda.html` | Hero dipendekkan dari `min-h-[90vh]` menjadi sekitar 300px dengan 2 CTA; baris aksi cepat berikon; kolom cek status inline; layanan populer dan berita sebagai carousel horizontal; teaser struktur, FAQ, dan WhatsApp |
| Struktur organisasi | `struktur-organisasi.html` | Bagan 1.050px diganti pohon vertikal: Camat, Sekretaris, lalu bidang sebagai accordion berisi anggota |
| Daftar layanan | `daftar-layanan.html` | Pencarian dan chip kategori yang menempel; 17 jenis surat dikelompokkan jadi 4 kategori dengan baris ringkas (kode, nama, panah); satu warna per kategori; bottom sheet pilih kelurahan (`?sheet=1`); "Alur" 4 langkah menggantikan infografis gambar; popup WhatsApp diganti banner |
| Download template | `download-template.html` | Pencarian; accordion per jenis surat; kartu template dengan nama berkas, ukuran, syarat yang dilipat, dan tombol Unduh penuh lebar; langkah cara pakai yang ringkas |

## Pemetaan ke kode (dasar `hotfix/v1.0.19`)

| Mockup | Blade yang perlu diubah | Data yang sudah ada |
|---|---|---|
| Navbar dan footer (`shared.js`) | `resources/views/layouts/landing.blade.php` (hapus `pt-[110px]` per halaman, perbaiki active-state `routeIs('portal.*')`, hapus tautan `#`) | route yang sudah ada |
| `beranda.html` | `portal/index.blade.php` | `$sliders`, `$beritaTerbaru` (dari `PortalPublikController@index`); layanan populer perlu query `JenisSurat` aktif |
| `struktur-organisasi.html` | `portal/struktur-organisasi.blade.php` | `PortalStrukturOrganisasi` (`parent_id`, `urutan`); partial rekursif `portal/partials/struktur-node.blade.php` dan `$strukturTree` sudah ada tetapi tidak terpakai |
| `daftar-layanan.html` | `services/index.blade.php` | `$services`, `$kelurahans`, `$popup`; kategori belum ada di data (perlu kolom atau pemetaan kode ke kategori) |
| `download-template.html` | `portal/template-surat/index.blade.php` | `$jenisSurats` dengan `templateSurats` (`syarat`, nama berkas); ukuran berkas perlu dihitung dari storage |

## Catatan dan keterbatasan

- Semua nama orang, NIP, dan nama bidang di struktur organisasi adalah **placeholder**. Nama seksi selain "Pemerintahan" dan "Kelompok Jabatan Fungsional" belum dicek ke data produksi.
- Nama berkas, ukuran, dan syarat template di mockup adalah contoh.
- Daftar kelurahan di bottom sheet adalah contoh kelurahan Kec. Landasan Ulin; implementasi memakai `Kelurahan` dengan `kecamatan_id` `6372010`.
- Kategori layanan (keterangan warga, kematian dan kehilangan, pernikahan, lembaga) adalah usulan, belum ada di database.
- Pencarian, chip, carousel, dan accordion pada mockup belum berfungsi penuh; hanya `<details>` dan menu yang interaktif.
- Perlu dicoba di perangkat nyata, termasuk kontras warna dan ukuran sentuh, sebelum implementasi.

## Render ulang preview

```bash
pip install websockets
python docs/ui-mockups/render-previews.py
```

Skrip memakai Chrome atau Edge (set `CHROME_PATH` bila perlu) lewat DevTools Protocol agar viewport tepat 375px. `--window-size` tidak dipakai karena Chrome headless memiliki lebar jendela minimum.
