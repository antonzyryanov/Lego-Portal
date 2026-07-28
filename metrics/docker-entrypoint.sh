#!/bin/sh
set -eu

# Persist metrics DB on the shared bind mount at /data.
mkdir -p /data
chmod 777 /data 2>/dev/null || chmod 755 /data || true

export PORT="${PORT:-8081}"
export METRICS_DB_PATH="${METRICS_DB_PATH:-/data/metrics.db}"

# Ensure existing DB files remain writable across restarts.
if [ -f "${METRICS_DB_PATH}" ]; then
  chmod 666 "${METRICS_DB_PATH}" 2>/dev/null || true
  chmod 666 "${METRICS_DB_PATH}-wal" 2>/dev/null || true
  chmod 666 "${METRICS_DB_PATH}-shm" 2>/dev/null || true
fi

/usr/local/bin/metrics-server &
APP_PID=$!

cleanup() {
  echo "shutting down"
  kill -TERM "${APP_PID}" 2>/dev/null || true
  kill -TERM "${HTTPD_PID:-}" 2>/dev/null || true
  wait "${APP_PID}" 2>/dev/null || true
  wait "${HTTPD_PID:-}" 2>/dev/null || true
}

trap cleanup INT TERM EXIT

echo "waiting for metrics-server on :${PORT}"
i=0
until wget -q -O /dev/null "http://127.0.0.1:${PORT}/health"; do
  i=$((i + 1))
  if [ "${i}" -ge 30 ]; then
    echo "metrics-server failed to become ready" >&2
    exit 1
  fi
  if ! kill -0 "${APP_PID}" 2>/dev/null; then
    echo "metrics-server exited prematurely" >&2
    exit 1
  fi
  sleep 1
done

echo "metrics-server is ready; starting Apache httpd"
httpd -DFOREGROUND &
HTTPD_PID=$!

# Keep PID 1 alive while both children run; exit if either dies.
while kill -0 "${APP_PID}" 2>/dev/null && kill -0 "${HTTPD_PID}" 2>/dev/null; do
  sleep 1
done

echo "a supervised process exited" >&2
exit 1
