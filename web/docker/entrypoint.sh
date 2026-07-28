#!/bin/bash
set -e

DB_PATH="${DB_DATABASE:-/data/lego.db}"

echo "[entrypoint] Waiting for SQLite database at ${DB_PATH}..."
for i in $(seq 1 90); do
    if [ -f "${DB_PATH}" ]; then
        echo "[entrypoint] Database file found."
        break
    fi
    if [ "${i}" -eq 90 ]; then
        echo "[entrypoint] ERROR: database file not found after waiting: ${DB_PATH}" >&2
        exit 1
    fi
    sleep 1
done

cd /var/www/html

mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R ug+rwx storage bootstrap/cache || true

# Ensure the shared SQLite file is writable by Apache/PHP
if [ -f "${DB_PATH}" ]; then
    chmod 666 "${DB_PATH}" || true
    chmod 777 "$(dirname "${DB_PATH}")" || true
fi

if [ -z "${APP_KEY}" ]; then
    echo "[entrypoint] APP_KEY missing; generating..."
    php artisan key:generate --force --no-interaction
fi

echo "[entrypoint] Running migrations..."
php artisan migrate --force --no-interaction

if [ "${RUN_SEEDERS:-true}" = "true" ]; then
    USER_COUNT="$(sqlite3 "${DB_PATH}" "SELECT COUNT(*) FROM users;" 2>/dev/null || echo 0)"
    if [ "${USER_COUNT}" = "0" ]; then
        echo "[entrypoint] Seeding database..."
        php artisan db:seed --force --no-interaction || true
    else
        echo "[entrypoint] Users already present; skipping seeders."
    fi
fi

php artisan config:clear --no-interaction || true
php artisan route:clear --no-interaction || true
php artisan view:clear --no-interaction || true

# Public disk for uploaded news images
php artisan storage:link --force --no-interaction || true

echo "[entrypoint] Starting Apache..."
exec apache2-foreground
