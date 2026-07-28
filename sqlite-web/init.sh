#!/bin/sh
set -e

DB_PATH="/data/lego.db"

mkdir -p /data

# Never overwrite an existing database — only create if missing.
if [ ! -f "${DB_PATH}" ]; then
    echo "[sqlite-web] Creating ${DB_PATH}..."
    sqlite3 "${DB_PATH}" "PRAGMA journal_mode=WAL; VACUUM;"
else
    echo "[sqlite-web] Persisted database found at ${DB_PATH}."
fi

# Keep DB writable for the web container (shared bind mount).
chmod 666 "${DB_PATH}" 2>/dev/null || true
chmod 666 "${DB_PATH}-wal" 2>/dev/null || true
chmod 666 "${DB_PATH}-shm" 2>/dev/null || true
chmod 777 /data || true

echo "[sqlite-web] Database ready at ${DB_PATH}. Keeping container alive..."
exec tail -f /dev/null
