#!/bin/sh
set -e

cd /var/www/html

mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    storage/app/private \
    storage/app/public \
    bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache

# Perintah satu kali (mis. `docker compose run --rm app php artisan ...`)
# dijalankan apa adanya, tanpa migrasi maupun server.
if [ "$#" -gt 0 ] && [ "$1" != "serve" ]; then
    exec "$@"
fi

if [ -z "${APP_KEY:-}" ]; then
    echo "APP_KEY belum diset." >&2
    echo "Buat kunci baru dengan:" >&2
    echo "  docker compose run --rm app php artisan key:generate --show" >&2
    echo "lalu set APP_KEY pada file .env di direktori proyek." >&2
    exit 1
fi

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    percobaan=0
    until su-exec www-data php artisan migrate --force --no-interaction; do
        percobaan=$((percobaan + 1))
        if [ "$percobaan" -ge 10 ]; then
            echo "Migrasi gagal setelah 10 percobaan." >&2
            exit 1
        fi
        echo "Database belum siap, ulangi dalam 3 detik ($percobaan/10)." >&2
        sleep 3
    done
fi

if [ "${SEED_ON_START:-false}" = "true" ]; then
    su-exec www-data php artisan db:seed --force --no-interaction
fi

# Manifest/cache dari mesin developer bisa menunjuk paket dev yang tidak ada di image.
rm -f bootstrap/cache/*.php
chown www-data:www-data bootstrap/cache

su-exec www-data php artisan optimize --no-interaction || \
    echo "Peringatan: optimize gagal, aplikasi tetap dijalankan tanpa cache." >&2

exec supervisord -c /etc/supervisord.conf