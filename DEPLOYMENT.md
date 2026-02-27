# 🚀 Panduan Deploy — SIMPEL Landasan Ulin

> **Branch Policy:** Gunakan Branch Versioning (misal `deploy/v1.0.0`) · **OS:** Ubuntu 22.04 LTS
> **Stack:** PHP 8.2 · Nginx · MySQL · Redis · Supervisor

---

## Prasyarat

- Server Ubuntu 22.04 LTS (fresh install)
- Akses root / sudo
- Domain sudah diarahkan ke IP server (Termasuk Subdomain Admin, misal: `simpel.id` & `admin.simpel.id`)

---

## Step 1 — Update System

```bash
sudo apt update && sudo apt upgrade -y
```

---

## Step 2 — Install PHP 8.2 & MySQL

```bash
# Tambah repository PHP
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP dan extension yang dibutuhkan
sudo apt install -y \
  php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring \
  php8.2-xml php8.2-zip php8.2-gd php8.2-bcmath \
  php8.2-curl php8.2-intl php8.2-redis php8.2-opcache

# Install MySQL
sudo apt install -y mysql-server
sudo systemctl enable --now mysql

# Amankan MySQL
sudo mysql_secure_installation

# Buat database dan user
sudo mysql -u root -p
```

```sql
CREATE DATABASE db_simpel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'simpel'@'localhost' IDENTIFIED BY 'ganti_password_ini';
GRANT ALL PRIVILEGES ON db_simpel.* TO 'simpel'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## Step 3 — Install Nginx & Composer

```bash
# Nginx
sudo apt install -y nginx
sudo systemctl enable nginx

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

---

## Step 4 — Install Redis

```bash
sudo apt install -y redis-server
sudo systemctl enable --now redis-server

# Verifikasi — harus balas PONG
redis-cli ping
```

---

## Step 5 — Clone Repository

```bash
sudo mkdir -p /var/www
cd /var/www

# Clone repository utama
git clone git@github.com:jklin12/simpel.git simpel
cd simpel

# Pindah ke branch versioning yang baru (contoh v1.0.0)
git checkout deploy/v1.0.0
``` 

---

## Step 6 — Install Dependencies & Frontend

```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

---

## Step 7 — Konfigurasi Environment

```bash
cp .env.example .env
nano .env
```

Isi bagian berikut. Perhatikan pembagian sesi (session) antar domain utama dan subdomain admin:

```env
APP_NAME="SIMPEL Landasan Ulin"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://simpel.id

# Konfigurasi Domain Utama dan Subdomain Admin
SESSION_DOMAIN=".simpel.id"       # Tambahkan TITIK (.) di awal agar sesi (login) bisa dibagi
ADMIN_DOMAIN="admin.simpel.id"    # Nama subdomain untuk login ke halaman admin
SESSION_SECURE_COOKIE=true        # Disarankan karena environment production harus SSL

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_simpel
DB_USERNAME=simpel
DB_PASSWORD=ganti_password_ini

# Cache & Queue — Redis
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# Storage — Local
FILESYSTEM_DISK=public

# Mail — SMTP
MAIL_MAILER=smtp
MAIL_HOST=smtp.provider-kamu.com
MAIL_PORT=587
MAIL_USERNAME=email@domain-kamu.id
MAIL_PASSWORD=password_email
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@domain-kamu.id
MAIL_FROM_NAME="${APP_NAME}"

# WhatsApp Notifikasi
WA_API_URL=http://url-wa-api-kamu/send-message
```

---

## Step 8 — Setup Aplikasi

```bash
cd /var/www/simpel

php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan optimize
```

---

## Step 9 — Set Permission

```bash
sudo chown -R www-data:www-data /var/www/simpel
sudo chmod -R 755 /var/www/simpel
sudo chmod -R 775 /var/www/simpel/storage /var/www/simpel/bootstrap/cache
```

---

## Step 10 — Konfigurasi Nginx (Domain & Subdomain)

```bash
sudo nano /etc/nginx/sites-available/simpel
```

Daftarkan **Subdomain** dan **Domain** di file konfigurasi virtual host yang sama:

```nginx
server {
    listen 80;
    
    # Daftarkan domain utama dan subdomain admin bersamaan
    server_name simpel.id admin.simpel.id;
    
    # Selalu arahkan document root ke satu titik public Laravel
    root /var/www/simpel/public;
    index index.php;

    charset utf-8;
    client_max_body_size 20M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    }

    location ~ /\.(?!well-known).* { deny all; }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/simpel /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## Step 11 — SSL (HTTPS)

```bash
sudo apt install -y certbot python3-certbot-nginx

# Buat SSL untuk kedua domain sekaligus
sudo certbot --nginx -d simpel.id -d admin.simpel.id

# Verifikasi auto-renew
sudo certbot renew --dry-run
```

---

## Step 12 — Queue Worker (Supervisor)

```bash
sudo apt install -y supervisor
sudo nano /etc/supervisor/conf.d/simpel-queue.conf
```

```ini
[program:simpel-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/simpel/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/simpel/storage/logs/queue.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start simpel-queue:*

# Cek status
sudo supervisorctl status
```

---

## Step 13 — Verifikasi Akhir

```bash
sudo systemctl status php8.2-fpm    # PHP-FPM
sudo systemctl status nginx          # Nginx
sudo systemctl status mysql          # MySQL
redis-cli ping                       # Redis → PONG
sudo supervisorctl status            # Queue worker
php artisan migrate:status           # Semua migrasi Ran
ls -la /var/www/simpel/public/storage  # Storage symlink
```

Buka browser → `https://simpel.id` dan `https://admin.simpel.id` ✅

---

## Checklist Go-Live

- [ ] `APP_DEBUG=false` dan `APP_ENV=production` di `.env`
- [ ] SSL aktif, HTTP redirect ke HTTPS (untuk 2 domain)
- [ ] Session membagikan `.namadomain.id`
- [ ] Semua migrasi berstatus **Ran**
- [ ] Queue worker berjalan (`supervisorctl status`)
- [ ] Upload file berhasil tersimpan
- [ ] Notifikasi WhatsApp terkirim
- [ ] Backup database dilakukan sebelum deploy

---

## Update / Re-deploy Aplikasi

Saat ingin melakukan upgrade *version*, gunakan *flow* berikut:

```bash
cd /var/www/simpel

# Ambil branch versi terbaru (contohnya sekarang naik v1.1.0)
git fetch origin
git checkout deploy/v1.1.0

# Update dependensi & frontend
composer install --optimize-autoloader --no-dev
npm install && npm run build

# Clear Laravel Cache & Jalankan Migrasi
php artisan migrate --force
php artisan optimize:clear
php artisan optimize

# Restart queue untuk refresh code yg jalan di background
sudo supervisorctl restart simpel-queue:*
```

---

## 🛠️ Rollback Darurat
Jika saat menjalankan versi baru terdapat error kritikal di Production, jalankan step berikut untuk kembali ke versi stabil sebelumnya:

```bash
cd /var/www/simpel

# Kembali ke branch terdahulu (misal deploy/stable atau versi sblmnya v0.9.0)
git checkout deploy/stable

# Sesuaikan/restore dependencies yg lama
composer install --optimize-autoloader --no-dev

# Bersihkan cache Laravel
php artisan optimize:clear
php artisan optimize

# Restart Queue Server
sudo supervisorctl restart simpel-queue:*
```
