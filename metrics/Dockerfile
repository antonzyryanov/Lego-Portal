# syntax=docker/dockerfile:1

# ---- build stage ----
FROM golang:1.25-alpine AS builder

WORKDIR /src

COPY go.mod go.sum ./
COPY vendor ./vendor
COPY . .

RUN CGO_ENABLED=0 GOOS=linux GOARCH=amd64 \
    go build -mod=vendor -trimpath -ldflags="-s -w" -o /out/metrics-server ./cmd/server

# ---- runtime stage: Apache + Go binary ----
FROM httpd:2.4-alpine

RUN apk add --no-cache ca-certificates wget \
    && rm -f /usr/local/apache2/conf/httpd.conf

COPY apache/httpd.conf /usr/local/apache2/conf/httpd.conf
COPY --from=builder /out/metrics-server /usr/local/bin/metrics-server
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh

RUN sed -i 's/\r$//' /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/metrics-server /usr/local/bin/docker-entrypoint.sh \
    && mkdir -p /data \
    && chown -R daemon:daemon /data

ENV PORT=8081 \
    METRICS_DB_PATH=/data/metrics.db \
    RABBITMQ_URL= \
    METRICS_API_TOKEN=

VOLUME ["/data"]

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD wget -q -O /dev/null http://127.0.0.1/health || exit 1

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
