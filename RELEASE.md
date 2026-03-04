# Release Notes

## [v1.0.1] - 2026-03-04

### Added / Added Features
- **Manajemen Sub Kategori Data Kelurahan**: Menambahkan fitur _dynamic dependent dropdown_ untuk sub kategori Fasilitas (seperti Tempat Ibadah, Pemakaman, Pendidikan, dan fasilitas Umum lainnya) dan input kelengkapan _Status Instansi_ (Negeri/Swasta).
- **Pemetaan Wilayah (RT/RW) pada Data Kelurahan**: Data Kelurahan kini dilengkapi dengan pencatatan unit geografis spesifik `rt` dan `rw`. Kolom filter pencarian Peta/Tabel Admin untuk `rt` dan `rw` juga telah ditambahkan.
- **Peta Interaktif Layanan Publik (Frontend)**: 
  - Marker detail di Peta sekarang menampilkan nama jenis fasilitas selengkapnya.
  - Implementasi *Live-Search Filtering*: Peta di beranda portal kini bisa melakukan pencarian dinamis (berdasarkan nama lokasi, kategori fasilitas, alamat, maupun letak spesifik lingkungan RT/RW).
  - Integrasi ikon Peta secara dinamis (khusus untuk *Tempat Ibadah* ikon menyesuaikan dengan agama tempat ibadahnya secara otomatis).

### Changed / Improvements
- **Perbaikan Roles & Access Control Kelurahan**: Role `admin_kelurahan` telah ditambahkan otomatisasi filtering, yaitu mereka hanya dapat menambah, memodifikasi, dan membaca entitas data milik Kelurahan mereka masing-masing saja. Input pilihan `kelurahan_id` pada saat manajemen data telah disembunyikan/di-hold otomatis ke _identifier_ akun tersebut.
- **Pembaruan UI Peta Beranda**: Mengubah durasi _carousel/slider_ berita menjadi 10 detik dan mematikan visibilitas pointer slider saat laman dibuka di tampilan _mobile_ guna menyediakan *Layout* peta yang diutamakan.
- **Skema Database**: Merombak kolom `kategori` tabel Data Kelurahan (`portal_data_kelurahans`) yang semula bertipe data `enum` menjadi `varchar(255)` untuk memberikan skalabilitas kategori *Fasilitas Umum* dsb di masa lalu dan depannya.

---

*This release note maintains tracking over incremental administrative adjustments bridging dynamic filtering maps and role scoping permissions.*
