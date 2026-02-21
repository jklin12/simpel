# ─────────────────────────────────────────────────────────────────────────────
# Makefile — Docker Shortcut Commands
# Usage: make [command]
# ─────────────────────────────────────────────────────────────────────────────

COMPOSE = docker compose

.PHONY: help up down build restart logs shell migrate seed fresh key minio-url queue-status

help:  ## Tampilkan semua perintah yang tersedia
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage: make \033[36m<target>\033[0m\n\nTargets:\n"} /^[a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2 }' $(MAKEFILE_LIST)

# ─── Container Lifecycle ──────────────────────────────────────────────────────

up:  ## Jalankan semua container (background)
	$(COMPOSE) up -d

down:  ## Hentikan dan hapus container
	$(COMPOSE) down

build:  ## Build ulang image (setelah ada perubahan Dockerfile/composer.json)
	$(COMPOSE) build --no-cache

restart:  ## Restart semua container
	$(COMPOSE) restart

start: build up setup  ## Build + jalankan + setup awal (first-time)

# ─── Application Setup ────────────────────────────────────────────────────────

setup:  ## Setup awal: copy env, generate key, migrate, storage link
	@echo "🔧 Setting up application..."
	@if not exist .env (copy .env.docker .env)
	$(COMPOSE) exec app php artisan key:generate --ansi
	$(COMPOSE) exec app php artisan migrate --force
	$(COMPOSE) exec app php artisan storage:link
	@echo "✅ Setup selesai!"

env:  ## Copy .env.docker ke .env
	copy .env.docker .env

key:  ## Generate APP_KEY baru
	$(COMPOSE) exec app php artisan key:generate --ansi

migrate:  ## Jalankan migrasi database
	$(COMPOSE) exec app php artisan migrate

migrate-fresh:  ## Fresh migrate + seed
	$(COMPOSE) exec app php artisan migrate:fresh --seed

seed:  ## Jalankan database seeder
	$(COMPOSE) exec app php artisan db:seed

cache-clear:  ## Bersihkan semua cache Laravel
	$(COMPOSE) exec app php artisan optimize:clear

# ─── Logs & Debugging ─────────────────────────────────────────────────────────

logs:  ## Tampilkan log semua container
	$(COMPOSE) logs -f

logs-app:  ## Tampilkan log container app saja
	$(COMPOSE) logs -f app

logs-queue:  ## Tampilkan log queue worker
	$(COMPOSE) logs -f queue

shell:  ## Masuk ke shell container app
	$(COMPOSE) exec app bash

shell-mysql:  ## Masuk ke MySQL CLI
	$(COMPOSE) exec mysql mysql -u simpel -psecret db_simpel

queue-status:  ## Cek status queue worker
	$(COMPOSE) exec app php artisan queue:monitor redis

# ─── MinIO ────────────────────────────────────────────────────────────────────

minio-ui:  ## Buka MinIO console di browser (Windows)
	start http://localhost:9001

minio-url:  ## Generate URL publik untuk file di MinIO
	@echo "MinIO API  : http://localhost:9000"
	@echo "MinIO UI   : http://localhost:9001"
	@echo "Bucket     : simpel"
	@echo "Username   : minioadmin | Password: minioadmin"

# ─── Useful Artisan ───────────────────────────────────────────────────────────

tinker:  ## Buka Laravel Tinker
	$(COMPOSE) exec app php artisan tinker

routes:  ## Tampilkan daftar route
	$(COMPOSE) exec app php artisan route:list

queue-restart:  ## Restart queue worker
	$(COMPOSE) exec app php artisan queue:restart
