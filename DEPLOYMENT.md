# 🚀 Panduan Deploy — SIMPEL Landasan Ulin

> **Stack yang berjalan di Docker:** Nginx · PHP-FPM · Redis · Queue Worker  
> **Di luar Docker:** MySQL (lokal host) · MinIO (server terpisah) · SMTP (eksternal)

---

## Prasyarat

Pastikan sudah terinstall di server:
- **Docker** ≥ 24 & **Docker Compose** ≥ 2
- **Git**
- **MySQL** sudah berjalan di host (bukan container)

---

## Step 1 — Clone Repositori

```bash
git clone <url-repo> /var/www/simpel
cd /var/www/simpel
```

---

## Step 2 — Buat File `.env`

```bash
cp .env.docker .env
nano .env
```

Isi bagian berikut sesuai kondisi server:

```env
# ── Aplikasi ──────────────────────────────────
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.id
APP_PORT=8080          # Ganti jika port 8080 sudah dipakai container lain

# ── Database (MySQL lokal di host) ────────────
DB_HOST=host.docker.internal   # JANGAN diubah, ini IP host dari dalam container
DB_DATABASE=db_simpel
DB_USERNAME=root
DB_PASSWORD=isi_password_mysql_kamu

# ── Redis ─────────────────────────────────────
REDIS_EXPOSE_PORT=6380   # Ganti jika port 6380 sudah dipakai

# ── MinIO (server terpisah) ───────────────────
MINIO_ACCESS_KEY=isi_access_key_minio
MINIO_SECRET_KEY=isi_secret_key_minio
MINIO_BUCKET=simpel
MINIO_URL=http://ip-server-minio:9000       # URL yang diakses dari browser/app
MINIO_ENDPOINT=http://ip-server-minio:9000  # URL yang diakses dari dalam container
AWS_ACCESS_KEY_ID=isi_access_key_minio
AWS_SECRET_ACCESS_KEY=isi_secret_key_minio
AWS_ENDPOINT=http://ip-server-minio:9000

# ── Mail (SMTP eksternal) ──────────────────────
MAIL_HOST=smtp.provider-kamu.com
MAIL_PORT=587
MAIL_USERNAME=email@domain-kamu.id
MAIL_PASSWORD=isi_password_email
MAIL_FROM_ADDRESS=noreply@domain-kamu.id

# ── WhatsApp API ───────────────────────────────
WA_API_URL=http://url-wa-api-kamu/send-message
```

---

## Step 3 — Izinkan MySQL Diakses dari Container

Dari dalam Docker, aplikasi mengakses MySQL via `host.docker.internal`.  
Pastikan MySQL mengizinkan koneksi dari luar `127.0.0.1`:

```bash
# Edit config MySQL
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
```

Ubah baris:
```ini
bind-address = 127.0.0.1
```
menjadi:
```ini
bind-address = 0.0.0.0
```

```bash
# Restart MySQL
sudo systemctl restart mysql

# Grant akses ke user DB dari semua host
mysql -u root -p
```
```sql
GRANT ALL PRIVILEGES ON db_simpel.* TO 'root'@'%' IDENTIFIED BY 'password_kamu';
FLUSH PRIVILEGES;
EXIT;
```

---

## Step 4 — Build & Jalankan Container

```bash
# Build image PHP-FPM (hanya perlu sekali)
docker compose build

# Jalankan semua container
docker compose up -d
```

Cek semua container berjalan:
```bash
docker compose ps
```

Output yang diharapkan:
```
NAME             STATUS
simpel_app       Up
simpel_nginx     Up
simpel_redis     Up
simpel_queue     Up
```

---

## Step 5 — Setup Aplikasi (Sekali Saja)

```bash
# Generate app key
docker compose exec app php artisan key:generate

# Jalankan migrasi database
docker compose exec app php artisan migrate --force

# Jalankan seeder (data awal)
docker compose exec app php artisan db:seed --force

# Optimasi untuk production
docker compose exec app php artisan optimize
```

---

## Step 6 — Setup MinIO Bucket

Di **server MinIO**, buat bucket dan set akses publik:

```bash
# Jalankan MinIO client (mc) untuk setup bucket
docker run --rm --network host minio/mc \
  alias set myminio http://localhost:9000 ACCESS_KEY SECRET_KEY

docker run --rm --network host minio/mc \
  mb --ignore-existing myminio/simpel

docker run --rm --network host minio/mc \
  anonymous set download myminio/simpel/public
```

Atau buka Web UI MinIO → `http://ip-server-minio:9001` → **Buckets** → **Create Bucket** → nama: `simpel`.

---

## Step 7 — Setup Nginx Reverse Proxy (SSL)

Agar aplikasi bisa diakses via domain dengan HTTPS, buat config Nginx di server:

```bash
sudo nano /etc/nginx/sites-available/simpel
```

```nginx
server {
    listen 80;
    server_name domain-anda.id www.domain-anda.id;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl;
    server_name domain-anda.id www.domain-anda.id;

    ssl_certificate     /etc/letsencrypt/live/domain-anda.id/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/domain-anda.id/privkey.pem;

    location / {
        proxy_pass         http://localhost:8080;   # Sesuaikan APP_PORT
        proxy_set_header   Host $host;
        proxy_set_header   X-Real-IP $remote_addr;
        proxy_set_header   X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header   X-Forwarded-Proto $scheme;
        client_max_body_size 20M;
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/simpel /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx

# Pasang SSL dengan Certbot
sudo certbot --nginx -d domain-anda.id -d www.domain-anda.id
```

---

## Step 8 — Verifikasi Akhir

```bash
# Cek semua container running
docker compose ps

# Cek log aplikasi (tidak ada error?)
docker compose logs --tail=30 app

# Cek queue worker aktif
docker compose logs --tail=20 queue

# Test koneksi ke database
docker compose exec app php artisan migrate:status

# Test koneksi ke MinIO
docker compose exec app php artisan tinker --execute="Storage::disk('minio')->put('test.txt', 'ok'); echo 'MinIO OK';"
```

Akses aplikasi: `https://domain-anda.id`

---

## Update / Re-deploy

```bash
cd /var/www/simpel

# Pull kode terbaru
git pull origin main

# Rebuild jika ada perubahan Dockerfile atau composer.json
docker compose build app queue

# Restart container
docker compose up -d

# Migrasi database baru
docker compose exec app php artisan migrate --force

# Bersihkan cache
docker compose exec app php artisan optimize:clear
docker compose exec app php artisan optimize

# Restart queue agar kode baru aktif
docker compose exec app php artisan queue:restart
```

---

## Perintah Berguna

```bash
# Masuk ke shell container app
docker compose exec app bash

# Lihat log real-time queue
docker compose logs -f queue

# Lihat semua container
docker compose ps

# Hentikan semua container
docker compose down

# Restart satu container
docker compose restart app

# Lihat failed jobs
docker compose exec app php artisan queue:failed

# Retry semua failed jobs
docker compose exec app php artisan queue:retry all
```

---

## Checklist Sebelum Go-Live

- [ ] `APP_DEBUG=false` di `.env`
- [ ] `APP_ENV=production` di `.env`
- [ ] SSL aktif di domain
- [ ] `docker compose ps` → semua container **Up**
- [ ] `php artisan migrate:status` → semua migrasi **Ran**
- [ ] Bucket MinIO `simpel` sudah dibuat
- [ ] Test upload file berhasil tersimpan di MinIO
- [ ] Test notifikasi WhatsApp terkirim
- [ ] Backup database sebelum setiap deploy
