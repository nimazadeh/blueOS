# Blue Studio OS

**Blue Studio OS** is the platform for **Blue Studio** — a digital product and
software studio. It is being built as a modular Laravel monolith: server-rendered
public site (Blade/Vite/SCSS), **Blue Control** admin (Livewire, `/admin`), MySQL 8,
bilingual English/Persian (RTL-first), SEO/security/accessibility as architecture —
not afterthoughts.

## Status

| Phase | Status |
|---|---|
| Phase 0 — Foundation, Discovery & Architecture | ✅ complete (`docs/phase-0-report.md`) |
| **Phase 1A — Foundation Core Implementation** | ✅ complete (`docs/phase-1a-report.md`) |
| Phase 1B — Design System & Public Experience | ⏳ next (not started) |

## What Phase 1A delivered

- Laravel 13 (PHP 8.5 target) project with modular monolith structure
  (`app/Core` + `app/Domains`).
- MySQL 8 foundation: users/sessions/settings/activity migrations; Docker Compose
  (PHP-FPM 8.5 + MySQL 8.4 + nginx + optional node) for reproducible development.
- Design-token SCSS foundation (colors/spacing/radius/motion/typography),
  logical-property RTL baseline, `en`/`fa` locale registry + direction system.
- Authentication foundation (admin guard, throttled login, session regeneration,
  no seeded credentials — `php artisan blue:create-admin`) and the protected
  `/admin` boundary (Blue Control placeholder, no CRUD yet).
- Core services boundaries: Settings, Slug, SEO (`MetaResolver`), Media
  (validation + disk), Activity (audit).
- Quality foundation: Pest 5 + PHPUnit 13.3, Pint, PHPStan/Larastan, Vite 8 +
  sass build, GitHub Actions CI (PHP 8.5 + MySQL 8.4), 8 test suites.
- Integration/code-style docs: `docs/development.md`,
  `docs/architecture-implementation.md`.

**Verification:** frontend build + PHP 8.5.10 syntax lint verified in the
workspace; full runtime suite runs on GitHub Actions CI (see
[`docs/environment.md`](docs/environment.md) and ADR-0014 for why local PHP/MySQL
execution is not possible in this sandbox).

## Quick start

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
# open http://localhost:8000
```

Full instructions: [`docs/development.md`](docs/development.md).

## Documentation

All documentation lives in [`docs/`](docs/README.md) — architecture decision
records (`docs/adr/`), design blueprints, security/SEO/accessibility specs,
testing/QA/deployment strategy, and the phase reports.
