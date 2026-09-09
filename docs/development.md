# Blue Studio OS — Local Development Guide

**Status:** Phase 1A. Target runtime: **PHP 8.5 · Laravel 13 · MySQL 8**.

---

## 1. Prerequisites

| Tool | Version | Purpose |
|---|---|---|
| PHP | **8.5** (min 8.4 — see `composer.json`) | application runtime |
| Composer | 2.x | PHP dependencies |
| MySQL | **8.x** (8.4 LTS recommended) | primary database |
| Node.js | 22.x (min 20.19) | Vite asset pipeline |
| npm | 10.x | frontend dependencies |
| Docker + Compose (optional) | latest | containerized dev environment |

## 2. Option A — Docker Compose (recommended, reproducible)

```bash
cp .env.example .env          # Docker defaults already point at the mysql service
docker compose up -d --build  # app (PHP 8.5-FPM) + mysql 8.4 + nginx

# Install PHP & frontend dependencies inside the app container:
docker compose exec app composer install
docker compose exec app cp -n .env.example .env 2>/dev/null || true
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec --user node npm ci   # node service is profile-gated:
docker compose --profile assets up node  # or run `npm ci && npm run build` on host
```

Open `http://localhost:8000`.

### Commands

| Action | Command |
|---|---|
| Start | `docker compose up -d --build` |
| Stop (keep volumes) | `docker compose down` |
| Reset database (wipe + reseed) | `docker compose exec app php artisan migrate:fresh --seed` |
| Full reset (delete data volume) | `docker compose down -v && docker compose up -d` |
| Logs | `docker compose logs -f app mysql nginx` |
| Run tests | `docker compose exec app composer test` |
| Frontend dev server | `docker compose exec app npm run dev` (Vite watches) |
| Frontend one-off build | `docker compose exec app npm run build` |

> The PHP image is a **copy of the real code** via a bind mount — no image
> rebuild needed for code changes. Rebuild only when `docker/php/Dockerfile`
> changes.

## 3. Option B — Native (Herd / local PHP + MySQL)

1. Install PHP 8.5 (+ extensions: `mbstring`, `intl`, `pdo_mysql`, `bcmath`,
   `zip`, `gd` — required by composer.json).
2. Start MySQL 8 and create the database/user (or use Laravel Herd's MySQL).
3. Copy `.env.example` to `.env`, then point `DB_HOST=127.0.0.1` and set your
   credentials (Docker values below are not valid for native MySQL).
4. Install + run:

```bash
composer install
cp -n .env.example .env 2>/dev/null || true
php artisan key:generate
php artisan migrate --seed
npm ci && npm run build
php artisan serve            # http://127.0.0.1:8000
```

For tests with the in-memory SQLite default: PHP needs `pdo_sqlite`, or run
`composer test` with the MySQL env vars from CI.

## 4. Environment variables (essential)

| Variable | Docker default | Native default | Notes |
|---|---|---|---|
| `APP_ENV` | `local` | `local` | never `production` with debug on |
| `APP_DEBUG` | `true` | `true` | must be `false` in staging/prod |
| `APP_KEY` | generated | generated | never commit; regenerate per env |
| `DB_HOST` | `mysql` | `127.0.0.1` | service name inside compose network |
| `DB_DATABASE` | `blueos` | `blueos` | |
| `DB_USERNAME` / `DB_PASSWORD` | `blueos` / `blueos` | your own | dev-only credentials |
| `APP_LOCALE` | `en` | `en` | default (unprefixed) locale |
| `APP_ENABLED_LOCALES` | `en,fa` | `en,fa` | locale registry |
| `MEDIA_DISK` | `local` | `local` | swap to `s3` later |

## 5. Test workflow

```bash
composer test            # full suite (Pest) — needs a build or SQLite-PHP
composer test:unit       # unit only (no database)
composer test:feature    # feature subset (refresh database each test)
composer format          # Pint (auto-fix)
./vendor/bin/pint --test # formatting check (CI)
./vendor/bin/phpstan analyse --memory-limit=1G   # static analysis
```

`phpunit.xml` defaults to **in-memory SQLite** so `composer test` works without
a DB server when `pdo_sqlite` is present. CI runs the same suite against
**MySQL 8.4** (see `.github/workflows/ci.yml`) — that is the authoritative
database gate.

## 6. Frontend

- **Dev:** `npm run dev` (Vite HMR; the app must be running on :8000).
- **Build:** `npm run build` → `public/build` (needed when tests render pages
  that use `@vite` without the dev server).
- Entries: `resources/scss/app.scss` + `resources/js/app.js` (public),
  `resources/scss/admin.scss` + `resources/js/admin.js` (Blue Control).
- SCSS layers (import order): `tokens → base → components → layouts →
  utilities → pages`; no raw values outside `tokens/`; **logical properties
  only** (no `*-left/*-right`).

## 7. Admin account (Blue Control)

Blue Control has **no seeded credentials** (no fake accounts). Create the real
operator account:

```bash
php artisan blue:create-admin you@example.com "Your Name"
# (password prompted, min 12 chars)
```

Then open `http://localhost:8000/admin`.

## 8. Sandbox note (this workspace)

This sandbox cannot run PHP/MySQL/Composer (no PHP binary, apt blocked,
Packagist blocked). What IS verified here: `npm ci` + `npm run build` (local
Node), PHP 8.5.10 syntax lint of every PHP file (WASM CLI), and the full suite
runs in GitHub Actions CI on PHP 8.5 + MySQL 8.4 (see
[`environment.md`](environment.md) and ADR-0014).

## 9. Troubleshooting

| Symptom | Fix |
|---|---|
| `ViteManifestNotFoundException` in tests | `npm run build` first |
| MySQL connection refused | container still starting: `docker compose up -d` + wait for health |
| `pdo_mysql` missing | use the Docker image or install the native extension |
| Port 3306 already in use | set `DB_PORT_FORWARD=3307` in `.env` |
| Port 8000 already in use | set `APP_HTTP_PORT=8001` in `.env` |
| `composer test` DB errors | CI uses MySQL; locally either install `pdo_sqlite` or set MySQL env vars |
