# Lego Portal

Docker-based LEGO fan portal with Apache servers, PHP + Laravel web app, Go Language metrics service, RabbitMQ message broker, and dedicated SQLite data containers.

## Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) for Windows
- Docker Compose v2 (`docker compose`)

## Quick start

From the project root (`C:\lego_portal`):

```powershell
copy .env.example .env
docker compose up --build
```

First boot builds images, creates SQLite files under `./data` if missing, waits for RabbitMQ, runs Laravel migrations/seeders, and starts all services.

## Services & ports

| Service | URL / port | Notes |
|---------|------------|--------|
| Web (Laravel + Apache) | http://localhost:8080 | Main site & `/admin` |
| Metrics (Go + Apache) | http://localhost:8081 | Metrics HTTP API |
| RabbitMQ AMQP | localhost:5672 | App messaging |
| RabbitMQ Management | http://localhost:15672 | User `lego` / `lego_secret` |

## Default credentials

After the web container seeds the database:

| Role | Email | Password |
|------|-------|----------|
| Admin | `admin@lego.local` | `password` |
| Moderator | `moderator@lego.local` | `password` |

- **Site:** http://localhost:8080
- **Admin panel:** http://localhost:8080/admin

### API token auth

```powershell
Invoke-RestMethod -Uri http://localhost:8080/api/login -Method POST -ContentType 'application/json' -Body '{"email":"admin@lego.local","password":"password"}'
```

Use the returned token as `Authorization: Bearer <token>`.

## Environment

Root `.env.example` lists all variables used by Compose and the apps. Important defaults:

- Web DB: `./data/web/lego.db` (bind-mounted to `/data/lego.db`)
- Metrics DB: `./data/metrics/metrics.db` (bind-mounted to `/data/metrics.db`)
- Both SQLite files persist on the host across `docker compose restart`, `down`, `up`, and image rebuilds
- RabbitMQ: `lego` / `lego_secret`
- Metrics API token: `lego_metrics_token_change_me` (send as `X-Metrics-Token`)

## Useful commands

```powershell
# Start in background
docker compose up --build -d

# Logs
docker compose logs -f web
docker compose logs -f metrics

# Stop (databases in ./data are kept)
docker compose down
```

## Data persistence

SQLite databases live on the host under `data/`:

| Database | Host path |
|----------|-----------|
| Web (Laravel) | `data/web/lego.db` |
| Metrics (Go) | `data/metrics/metrics.db` |

These bind mounts keep all data through container restarts and `docker compose down` / `up`. Delete the files in `data/` only if you intentionally want a wipe.

## Architecture notes

- `sqlite_web` / `sqlite_metrics` Alpine containers create empty DB files **only if missing**, then stay alive so the shared bind mounts are ready for the app containers.
- Laravel entrypoint waits for `/data/lego.db`, runs `migrate`, and seeds only when the `users` table is empty.
- Apache on the web image enables rewrite/headers/expires/deflate, sets security headers, `LimitRequestBody` (10 MiB), and `RequestReadTimeout`. Laravel handles CSRF, Eloquent bindings (SQLi), Sanctum tokens, and rate limiters.
- Design tokens live in `web/public/design/` (and mirrored under `web/resources/design/`): colors, fonts, spacing, animations.
- LEGO set images are seeded from the Rebrickable CDN (`cdn.rebrickable.com`).
