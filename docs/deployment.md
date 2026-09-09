# Blue Studio OS — Deployment Strategy

**Status:** Accepted (Phase 0). Direction is fixed; provider choice is an
infrastructure decision made with the owner when Phase 1 begins (no provider is
locked in).

**Phase 1A update:** the *foundation* of this strategy now exists in-repo —
`docker-compose.yml` (app/nginx/mysql/node), `docker/php/Dockerfile` (PHP 8.5-FPM
with the Laravel extension set), `docker/nginx/default.conf`, and
`.github/workflows/ci.yml` (PHP 8.5 + MySQL 8.4 + Vite build + tests). Staging/
production image building and promotion remain future work.

---

## 1. Environments

| Env | Purpose | Data | Host (recommended direction) |
|---|---|---|---|
| `local` | developer machine | MySQL (Herd/Docker/local) | any machine |
| `development` (optional sandbox/integration) | integration | MySQL 8, seed-only | same as staging infrastructure |
| `staging` | pre-prod QA | anonymized/seed data (never prod copy) | single VPS / PaaS app |
| `production` | public | real | managed platform (PaaS or VPS + managed MySQL) |

**Rule: never use staging with production data unless explicitly required and
approved (GDPR/data handling).**

## 2. Release model

- Git-based: `main` has deployable code; tags/push → CI → build → deploy to staging →
  manual QA gate → promote to production.
- Blue Control content changes are separate from code deploys (content is data;
  code ships via git).
- Zero-downtime at runtime: queue/workers graceful restart; `php artisan
  migrate --force` before symlink/rollout; config/route cache rebuild.
- Rollback: previous release container/image retained; DB migrations designed
  reversible (down migrations) in v1; if a migration is destructive, an explicit
  `--step` plan + backup restore path documented.

## 3. Artifacts & build

| Step | Action |
|---|---|
| Build (CI) | `composer install --no-dev --optimize-autoloader` → `npm ci && npm run build` → `php artisan config:cache route:cache view:cache` → package image |
| Assets | Vite output in `/public/build` packaged with release; CDN optional later |
| Migrations | run in deploy step, **before** new code serves traffic |
| Storage | artifacts + uploads on persistent volume/object storage (never inside container image) |

## 4. Provisioning (production candidate topologies)

**Option A — PaaS (recommended for velocity):** Laravel Forge/Heroku-style →
managed PHP runtime, env vars, cron, queue workers, backup.
**Option B — VPS + nginx:** full control, same app; nginx config ships in `deploy/`
(Phase 1 scaffold): TLS, HTTP/2+, security headers, static cache, strict gzip/brotli,
`deny /admin` public cache, `location /media` aliasing.
**Option C — Docker Compose on single VPS** (staging/full control):
`php-fpm` + `nginx` (`nginx:alpine`) + `mysql:8` + optional `redis`; volume for media;
`compose` files in `deploy/`.

**Recommended Phase 1 direction:** Laravel on managed platform (A) with MySQL 8
managed service, or Option C for owner-managed. Sandbox constraints are documented
separately ([`environment.md`](environment.md)) — the sandbox cannot run PHP/MySQL
and is not a deployment target.

## 5. Database deployment

- MySQL 8.0+ (8.4 LTS preferred), `utf8mb4`, managed backups (daily + point-in-time).
- Migrations via artisan in deploy; never edit production schema manually.
- Backup restore drill scheduled (quarterly), documented in Phase 1 ops notes.
- Slow query log + index review in staging before prod.

## 6. Media & static delivery

- Local disk in dev; production: object storage (`s3`-compatible) or persistent
  volume + optional CDN. Public URLs versioned (`?v=`) for cache invalidation.
- `storage:link` for media in local/dev (public disk); production media disk
  configured via env `MEDIA_DISK`.
- Uploads never stored in git; backups cover media volume/object storage.

## 7. CI/CD (GitHub Actions, Phase 2)

```
PR: lint → test (MySQL service) → build → audit → E2E (playwright) → Lighthouse
push to main: tag → build image → deploy staging (auto) → smoke tests
promote: manual approval → prod deploy → health check → rollback on failure
```

Secrets (APP_KEY, DB creds, provider tokens) stored as GitHub Actions secrets /
platform secret manager — never in repo.

## 8. Ops & maintenance

- Cron: `schedule:run` (sitemap regen, backup integrity, orphan media cleanup).
- Queues: `database` driver v1 (simple, no Redis); production upgrade to Redis when
  volume justifies (deployment config only).
- Logs: stdout (container) or file; rotation; retention 30 days; error alerting Phase 5.
- Uptime monitoring + SSL renewal via platform/Let's Encrypt.

## 9. Security in deployment

- `APP_DEBUG=false` in staging/prod; strict env validation at boot
  (`APP_ENV`, `APP_KEY`, `APP_URL`).
- TLS everywhere (HSTS); secrets never in env examples/commits; `.env` ignored.
- Rate limits and CSP configured in nginx/app per [`security.md`](security.md).
- Admin: 2FA (Phase 3), IP logging, audit.

## 10. Deferred decisions

- Exact provider (PaaS vs VPS vs container orchestrator) — Phase 1 with owner.
- CDN (Cloudflare vs provider) — when traffic/payload demands; architecture already
  supports it.
- Redis for cache/queue — Phase 5 or on load evidence.
- Multi-region/data residency (Persian market considerations) — future phase.
- Backup restore automation in CI — Phase 5 (documented today).
