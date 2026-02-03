# Simpel Admin Starter Template

Project ini telah dikonfigurasi sebagai **Starter Template** yang solid untuk pengembangan aplikasi administrasi pemerintahan atau sistem informasi berbasis wilayah.

## 🚀 Technology Stack
- **Framework**: Laravel 10/11
- **Styling**: TailwindCSS (via Vite)
- **Icons**: Heroicons (SVG)
- **Alerts**: SweetAlert2
- **Auth & ACL**: Spatie Laravel Permission

## ✨ Fitur Tersedia (Out-of-the-box)

### 1. Authentication & Security
- Halaman Login kustom (Responsive & Modern)
- Middleware Role-based (`role:super_admin`, dll)
- Redirect logic berdasarkan Role setelah login

### 2. User & Role Management
- **Users**: CRUD User, Assign Role, Assign Lokasi (Kab/Kec/Kel auto-show)
- **Roles**: CRUD Roles, Assign Permissions
- **Permissions**: CRUD Permissions
- Visualisasi hierarki akses yang jelas

### 3. Master Data Wilayah (Hierarkis)
- **Kabupaten**: CRUD Data Kabupaten
- **Kecamatan**: CRUD Data Kecamatan (Relation to Kabupaten)
- **Kelurahan**: CRUD Data Kelurahan (Relation to Kecamatan)
- **Fitur**: Pencarian (Search), Pagination Custom (Tailwind), Validasi Unik

### 4. UI/UX Components
- **Sidebar**: Responsive, Collapsible Menu, Sub-menu support (Dropdown)
- **Topbar**: Profile dropdown, Notifications UI ready
- **Pagination**: Kustomisasi view Tailwind (Light mode forced)
- **Search Bar**: Komponen pencarian standar di setiap header index

## 🛠 Cara Menggunakan untuk Project Baru

Jika Anda ingin menggunakan project ini sebagai dasar untuk project baru (misal: `surat-banjarmasin`), ikuti langkah ini:

### 1. Clone / Copy Project
Copy seluruh folder project ini ke folder baru, ATAU jika menggunakan Git:
```bash
git clone url-repo-ini nama-project-baru
cd nama-project-baru
rm -rf .git # Hapus history git lama jika ingin mulai fresh
git init    # Inisialisasi git baru
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Setup Environment
Copy file `.env.example` ke `.env` dan sesuaikan database:
```bash
cp .env.example .env
php artisan key:generate
```
*Edit `.env` dan sesuaikan DB_DATABASE, APP_NAME, dll.*

### 4. Database Setup
Jalankan migrasi dan seeder bawaan untuk membuat akun Super Admin dan Data Wilayah awal:
```bash
php artisan migrate:seed
```
*Seeder `DatabaseSeeder` otomatis memanggil `RolePermissionSeeder`, `MasterLocationSeeder`, dan `UserSeeder`.*

### 5. Build Assets
```bash
npm run build
```

## 📝 Konfigurasi Penting

- **Ganti Nama Aplikasi**: Edit `.env` (`APP_NAME`) dan `resources/views/layouts/partials/sidebar.blade.php` (Logo Text).
- **Menu Sidebar**: Tambahkan menu baru di `resources/views/layouts/partials/sidebar.blade.php`.
- **Modul Baru**:
    1. Buat Model & Migration (`php artisan make:model NamaModel -m`)
    2. Buat Controller (`php artisan make:controller NamaController --resource`)
    3. Daftarkan Route di `routes/web.php`
    4. Buat Views (Copy dari folder `resources/views/admin/master/kabupaten` sebagai referensi struktur)

---
*Dibuat dengan bantuan AI Assistant (Simpel Team)*
