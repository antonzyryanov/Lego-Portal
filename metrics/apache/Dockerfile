# Optional alternate image that only builds/runs Apache as a reverse proxy.
# Prefer the root Dockerfile which ships Apache + the Go binary together.
FROM httpd:2.4-alpine

COPY httpd.conf /usr/local/apache2/conf/httpd.conf

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
    CMD wget -q -O /dev/null http://127.0.0.1/health || exit 1
