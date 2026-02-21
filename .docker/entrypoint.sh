#!/bin/sh
set -e

# Pastikan vendor ada — volume mount bisa menimpa vendor dari image
if [ ! -f "/var/www/vendor/autoload.php" ]; then
    echo ">>> vendor/autoload.php tidak ditemukan, menjalankan composer install..."
    cd /var/www && composer install --no-scripts --prefer-dist --no-interaction --no-dev
    composer dump-autoload --optimize
    echo ">>> Composer selesai."
fi

# Fix permissions storage & cache
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true

exec "$@"
