# 📦 Panduan Deployment — SIMPEL Landasan Ulin

> **Sistem Pengelolaan Layanan Elektronik Kelurahan**  
> Laravel 10 · PHP 8.2 · MySQL · Redis · MinIO

---

## Daftar Isi

1. [Kebutuhan Server](#1-kebutuhan-server)
2. [Persiapan Awal](#2-persiapan-awal)
3. [Deploy via Docker *(Rekomendasi)*](#3-deploy-via-docker-rekomendasi)
4. [Deploy Manual (VPS/Shared Hosting)](#4-deploy-manual-vpsshared-hosting)
5. [Konfigurasi Environment](#5-konfigurasi-environment)
6. [Konfigurasi Storage (MinIO)](#6-konfigurasi-storage-minio)
7. [Queue & Notifikasi](#7-queue--notifikasi)
8. [Update / Re-deploy](#8-update--re-deploy)
9. [Troubleshooting](#9-troubleshooting)

---

## 1. Kebutuhan Server

### Minimum
| Komponen | Spesifikasi |
|---|---|
| CPU | 2 vCPU |
| RAM | 2 GB |
| Storage | 20 GB SSD |
| OS | Ubuntu 22.04 LTS / Debian 12 |

### Software yang Diperlukan
| Software | Versi | Keterangan |
|---|---|---|
| PHP | ≥ 8.1 | ext: pdo_mysql, mbstring, gd, zip, bcmath, redis |
| MySQL | ≥ 8.0 | Sudah running (tidak dicontainer-kan) |
| Nginx | ≥ 1.20 | Web server |
| Composer | ≥ 2.x | PHP dependency manager |
| **Docker** | ≥ 24.x | Untuk metode Docker |
| **Docker Compose** | ≥ 2.x | Untuk metode Docker |

---

## 2. Persiapan Awal

### Clone Repositori

```bash
git clone <url-repo> /var/www/simpel
cd /var/www/simpel
```

### Izin Direktori

```bash
chown -R www-data:www-data /var/www/simpel
chmod -R 755 /var/www/simpel
chmod -R 775 /var/www/simpel/storage /var/www/simpel/bootstrap/cache
```

---

## 3. Deploy via Docker *(Rekomendasi)*

Metode ini menjalankan Nginx, PHP-FPM, Redis, MinIO, dan Queue Worker via Docker Compose.  
MySQL tetap menggunakan instance yang sudah berjalan di server.

### Langkah-langkah

**a. Siapkan Environment**

```bash
cp .env.docker .env
nano .env    # Edit sesuai konfigurasi server (DB, MinIO credentials, dll)
```

Variabel penting yang **wajib** disesuaikan:

```env
APP_URL=https://domain-anda.id
APP_KEY=            # Diisi setelah key:generate

DB_HOST=host.docker.internal   # Atau IP server jika MySQL pada host yang sama
DB_DATABASE=db_simpel
DB_USERNAME=root
DB_PASSWORD=password_mysql_anda

MINIO_ACCESS_KEY=ganti_dengan_key_kuat
MINIO_SECRET_KEY=ganti_dengan_secret_kuat
MINIO_BUCKET=simpel
MINIO_URL=https://storage.domain-anda.id   # URL publik MinIO

WA_API_URL=http://url-whatsapp-api-anda/send-message
```

**b. Pastikan MySQL Dapat Diakses dari Container**

```sql
-- Jalankan di MySQL server
GRANT ALL PRIVILEGES ON db_simpel.* TO 'root'@'%' IDENTIFIED BY 'password_anda';
FLUSH PRIVILEGES;
```

Jika MySQL bind ke `127.0.0.1`, ubah di `/etc/mysql/mysql.conf.d/mysqld.cnf`:
```ini
bind-address = 0.0.0.0
```
Lalu restart: `systemctl restart mysql`

**c. Build dan Jalankan**

```bash
# Build image (sekali saja, atau jika Dockerfile berubah)
docker compose build

# Jalankan semua service
docker compose up -d

# Generate app key
docker compose exec app php artisan key:generate

# Jalankan migrasi
docker compose exec app php artisan migrate --force

# Jalankan seeder (jika ada data awal)
docker compose exec app php artisan db:seed --force
```

**d. Verifikasi**

```bash
# Cek status semua container
docker compose ps

# Lihat log
docker compose logs --tail=50 app
docker compose logs --tail=50 queue
```

**e. Port yang Digunakan**

| Service | Port | URL |
|---|---|---|
| Aplikasi | 8080 | `http://server-ip:8080` |
| MinIO API | 9000 | `http://server-ip:9000` |
| MinIO UI | 9001 | `http://server-ip:9001` |

> **Tip:** Letakkan Nginx/Caddy di depan port 8080 untuk handle SSL dan custom domain.

---

## 4. Deploy Manual (VPS/Shared Hosting)

Untuk server tanpa Docker, install langsung di atas OS.

### a. Install Dependencies

```bash
# PHP 8.2 + extensions
apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring \
  php8.2-xml php8.2-zip php8.2-gd php8.2-bcmath php8.2-redis \
  php8.2-curl php8.2-intl

# Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# Redis
apt install -y redis-server
systemctl enable --now redis-server

# Nginx
apt install -y nginx
```

### b. Setup Project

```bash
cd /var/www/simpel

# Install composer packages (production, tanpa dev dependencies)
composer install --optimize-autoloader --no-dev

# Setup env
cp .env.example .env
nano .env   # Sesuaikan konfigurasi

php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan optimize
```

### c. Konfigurasi Nginx

```nginx
server {
    listen 80;
    server_name domain-anda.id www.domain-anda.id;
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
ln -s /etc/nginx/sites-available/simpel /etc/nginx/sites-enabled/
nginx -t && systemctl reload nginx
```

### d. SSL dengan Certbot

```bash
apt install -y certbot python3-certbot-nginx
certbot --nginx -d domain-anda.id -d www.domain-anda.id
```

### e. Queue Worker dengan Supervisor

```bash
apt install -y supervisor
```

Buat file `/etc/supervisor/conf.d/simpel-queue.conf`:

```ini
[program:simpel-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/simpel/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/simpel/storage/logs/queue.log
stopwaitsecs=3600
```

```bash
supervisorctl reread
supervisorctl update
supervisorctl start simpel-queue:*
```

---

## 5. Konfigurasi Environment

File `.env` — variabel penting untuk production:

```env
APP_NAME="SIMPEL Landasan Ulin"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.id

# Custom Port (sesuaikan agar tidak bentrok dengan container lain)
APP_PORT=8080             # Port Nginx
REDIS_EXPOSE_PORT=6380    # Port Redis ke host
MAILPIT_SMTP_PORT=1025
MAILPIT_UI_PORT=8025

# Database
DB_CONNECTION=mysql
DB_HOST=host.docker.internal   # IP host machine dari dalam container
DB_PORT=3306
DB_DATABASE=db_simpel
DB_USERNAME=root
DB_PASSWORD=password_mysql_anda

# Redis
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=redis
REDIS_PORT=6379

# Storage — MinIO Eksternal
FILESYSTEM_DISK=minio
MINIO_ACCESS_KEY=key_panjang_dan_acak
MINIO_SECRET_KEY=secret_panjang_dan_acak
MINIO_BUCKET=simpel
MINIO_URL=https://storage.domain-anda.id      # URL publik (dari browser/app)
MINIO_ENDPOINT=http://ip-server-minio:9000    # Endpoint dari dalam container
AWS_ACCESS_KEY_ID=key_panjang_dan_acak
AWS_SECRET_ACCESS_KEY=secret_panjang_dan_acak
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=simpel
AWS_ENDPOINT=http://ip-server-minio:9000
AWS_USE_PATH_STYLE_ENDPOINT=true

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.provider-anda.com
MAIL_PORT=587
MAIL_USERNAME=email@domain-anda.id
MAIL_PASSWORD=email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@domain-anda.id

# WhatsApp API
WA_API_URL=http://url-whatsapp-api/send-message
```

### Custom Port — Cara Ganti

Edit nilai di `.env` sebelum menjalankan `docker compose up`:

```env
APP_PORT=9090          # Ganti port Nginx
REDIS_EXPOSE_PORT=6381 # Ganti port Redis
```

Tidak perlu edit `docker-compose.yml` sama sekali.

---

## 6. Konfigurasi Storage (MinIO Eksternal)

MinIO berjalan di **server terpisah** dan diakses oleh aplikasi via API S3-compatible.

### Setup MinIO di Server Terpisah

```bash
# Jalankan MinIO di server storage
docker run -d \
  --name minio \
  -p 9000:9000 \
  -p 9001:9001 \
  -e MINIO_ROOT_USER=your_access_key \
  -e MINIO_ROOT_PASSWORD=your_secret_key \
  -v /data/minio:/data \
  minio/minio server /data --console-address ":9001"
```

### Buat Bucket

```bash
# Via MinIO Client (mc)
docker run --rm --network host minio/mc \
  alias set myminio http://localhost:9000 your_access_key your_secret_key

docker run --rm --network host minio/mc \
  mb myminio/simpel

# Set folder public dapat diakses publik
docker run --rm --network host minio/mc \
  anonymous set download myminio/simpel/public
```

Atau buka **MinIO Web UI** → `http://ip-server-minio:9001` → Buckets → Create Bucket `simpel`.

### Pastikan Aplikasi Dapat Konek

Di `.env`, isi:
- `MINIO_ENDPOINT` = URL yang bisa diakses **dari dalam container** (bisa IP internal server)
- `MINIO_URL` = URL yang bisa diakses **dari browser** (bisa domain/IP publik)

```env
MINIO_ENDPOINT=http://192.168.1.100:9000   # IP internal server MinIO
MINIO_URL=https://storage.domain-anda.id   # URL publik
```

### Reverse Proxy MinIO (untuk domain custom)

```nginx
server {
    listen 443 ssl;
    server_name storage.domain-anda.id;

    location / {
        proxy_pass http://localhost:9000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        client_max_body_size 100M;
    }
}
```

---

## 7. Queue & Notifikasi

Notifikasi WhatsApp dikirim secara **asynchronous** melalui Laravel Queue.

### Pastikan Queue Worker Berjalan

**Docker:**
```bash
docker compose ps queue    # Cek status
docker compose logs -f queue  # Lihat log real-time
```

**Manual (Supervisor):**
```bash
supervisorctl status simpel-queue:*
```

### Test Manual Queue

```bash
# Docker
docker compose exec app php artisan tinker
# >>> \App\Models\PermohonanSurat::first()->notify(new \App\Notifications\PermohonanCreatedWhatsapp(...))

# Manual
php artisan tinker
```

### Monitor Failed Jobs

```bash
# Lihat failed jobs
php artisan queue:failed

# Retry semua failed jobs
php artisan queue:retry all

# Hapus failed jobs lama
php artisan queue:flush
```

---

## 8. Update / Re-deploy

### Via Docker

```bash
# Pull kode terbaru
git pull origin main

# Jika ada perubahan composer.json → rebuild image
docker compose build app queue

# Restart containers
docker compose up -d

# Jalankan migrasi baru
docker compose exec app php artisan migrate --force

# Bersihkan cache setelah update
docker compose exec app php artisan optimize:clear
docker compose exec app php artisan optimize

# Restart queue agar kode baru aktif
docker compose exec app php artisan queue:restart
```

### Via Manual

```bash
git pull origin main
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan optimize:clear
php artisan optimize
php artisan queue:restart
```

---

## 9. Troubleshooting

### Container tidak bisa konek ke MySQL lokal

```bash
# Pastikan MySQL listen ke 0.0.0.0
grep bind-address /etc/mysql/mysql.conf.d/mysqld.cnf

# Grant akses dari semua host
mysql -u root -p -e "GRANT ALL ON db_simpel.* TO 'root'@'%'; FLUSH PRIVILEGES;"
```

### Error 502 Bad Gateway

```bash
# Cek container app PHP-FPM running
docker compose ps app
docker compose logs app

# Restart
docker compose restart app nginx
```

### Storage tidak accessible

```bash
# Pastikan bucket sudah ada di MinIO
docker compose exec minio-init mc ls myminio/simpel

# Buat ulang symlink storage
docker compose exec app php artisan storage:link
```

### Queue job tidak diproses

```bash
# Cek apakah QUEUE_CONNECTION=redis di .env
docker compose exec app php artisan env | grep QUEUE

# Restart worker
docker compose restart queue
docker compose exec app php artisan queue:restart
```

### Permission denied pada storage

```bash
# Docker
docker compose exec app chown -R www-data:www-data /var/www/storage
docker compose exec app chmod -R 775 /var/www/storage

# Manual
chown -R www-data:www-data /var/www/simpel/storage
chmod -R 775 /var/www/simpel/storage
```

---

## Checklist Pre-Launch

- [ ] `APP_DEBUG=false` di `.env`
- [ ] `APP_ENV=production` di `.env`
- [ ] SSL aktif (HTTPS)
- [ ] Queue worker berjalan
- [ ] MinIO bucket tersedia dan dapat diakses publik
- [ ] Database migration sudah dijalankan
- [ ] `php artisan optimize` sudah dijalankan
- [ ] WhatsApp API URL sudah benar dan dapat diakses
- [ ] Test kirim notifikasi
- [ ] Backup database sebelum deploy
