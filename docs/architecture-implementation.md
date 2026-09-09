# Blue Studio OS — Architecture: Implementation

**Status:** Phase 1A (foundation implemented). This file documents the *actual
implemented* structure and the conventions; the full blueprint remains in
[`architecture.md`](architecture.md).

---

## 1. Implemented structure

```
app/
├── Core/                        # shared kernel (cross-cutting, no domain logic)
│   ├── Activity/ActivityLogger.php
│   ├── Media/MediaService.php
│   ├── Seo/Meta.php + MetaResolver.php
│   ├── Settings/SettingsService.php
│   └── Slug/SlugService.php
├── Domains/                     # bounded domains — boundaries only in 1A
│   ├── Products/ · Portfolio/ · Lab/ · Services/ · Content/ · Leads/
├── Http/
│   ├── Controllers/
│   │   ├── HomeController.php
│   │   ├── LocaleController.php
│   │   ├── Admin/Auth/LoginController.php
│   │   ├── Admin/DashboardController.php
│   │   └── System/HealthController.php
│   └── Middleware/
│       ├── RedirectIfAdminAuthenticated.php
│       └── SetLocale.php
├── Models/           User · Settings · ActivityLog
├── Providers/        AppServiceProvider (Core service bindings)
└── Support/          Locale (direction/lang helpers)
```

```
resources/
├── scss/   tokens/ base/ components/ layouts/ utilities/ pages/  (app.scss + admin.scss)
├── js/     app.js · admin.js · modules/{core,locale-switcher}.js
└── views/  layouts/{app,admin,guest}.blade.php · home · admin/{login,dashboard} ·
            errors/{404,500}
```

```
routes/   web.php (public + locale + health) · admin.php (/admin, auth foundation)
database/ migrations (users/sessions/cache/jobs/settings/activity_logs) · factories · seeder
docker/   php/Dockerfile · nginx/default.conf · docker-compose.yml (root)
.github/  workflows/ci.yml
tests/    Pest suites (Unit + Feature) — see docs/testing.md
```

## 2. Boundaries & rules (enforced by convention + CI review)

1. **HTTP controllers are thin** — validation in FormRequests/actions, no domain
   logic. (1A has only read/invoke controllers; domain CRUD lands later.)
2. **Domain modules never import each other's models.** Cross-cutting needs go
   through `App\Core\*`.
3. **Views never contain business logic or raw values** — data arrives from
   controllers/services; styling uses tokens only.
4. **Core services are the only components** that touch settings/media/seo/audit
   infrastructure. Domain code calls them; nothing bypasses them.
5. **Engine** — `App\Core\Seo\MetaResolver` + `Meta` value object are the only
   SEO producers in 1A; per-page SEO records land with the SEO domain.
6. **Locale & direction** — `App\Support\Locale` is the single authority for
   `lang`/`dir`. Views read it; nobody hardcodes direction.
7. **RTL** — CSS uses logical properties throughout (verified: zero
   `*-left/*-right` in compiled output).
8. **No fake content** — seeder writes only true platform defaults
   (`site.name`); no demo users, no business content, no fabricated numbers.

## 3. Implemented vs deferred (Phase 1A)

**Implemented**

- Laravel 13 skeleton (framework `^13.23`), PHP `^8.4` constraint (runtime 8.5),
  Pest 5 + PHPUnit 13.3, Pint, Larastan/PHPStan, Vite 8 + sass.
- Modular monolith: `App\Core` + `App\Domains` (boundaries), AppServiceProvider
  bindings.
- Design tokens (colors/spacing 4–128/radius/motion/typography/shadows) +
  SCSS layer architecture + RTL logical-property foundation + reduced-motion
  baseline.
- Locale foundation: `en`/`fa`, `config/blue.php` locale registry,
  `SetLocale` middleware, `LocaleController`, lang files (UI strings only),
  RTL attribute wiring; per-locale SEO output arrives later.
- Auth foundation: `admin` guard, rate-limited login (5/min), session
  regeneration, inactive-user lockout, `blue:create-admin` Artisan command,
  admin logout.
- Admin boundary: `/admin` route group, protected dashboard placeholder
  (auth check + noindex + guest redirect), admin/guest layouts.
- DB foundation: users (+locale/is_active/last_login_at), sessions, cache,
  jobs, settings, activity_logs migrations; `SettingsService` (cached),
  `ActivityLogger`.
- Security: CSRF everywhere, FormRequest-style validation on login, `Email`
  validation, `is_active` check, no secrets in repo, secure session defaults
  audited, `.env.example` only placeholders, X-Robots noindex on admin,
  production-safe error pages (404/500).
- Error handling: branded 404/500, `/health` endpoint with DB check,
  structured logging config retained from skeleton.
- CI foundation: GitHub Actions — Pint, PHPStan, Pest (MySQL 8.4 service),
  Vite build, `composer audit`-ready.
- Smoke tests: home, health/DB, auth (login/logout/protected/inactive),
  admin boundary, RTL/locale, settings, activity, media validation, slug, meta.
- Local development: `docker-compose.yml` (app/nginx/mysql/node-profile),
  `docker/php/Dockerfile`, nginx config, `.env.example`, development guide.

**Deferred (by design, no work started)**

- Complete homepage / public experience (Phase 1B).
- Three.js / GSAP / Lenis / any motion library (tokens + reduced-motion only).
- Admin CRUD, Livewire, roles/permissions tables, dashboard metrics.
- Product/portfolio/lab/service/blog/lead domain tables + forms.
- Media registry/variants pipeline (validation + storage boundary only).
- Translations beyond UI strings; Persian content.
- Sitemap, redirect model, hreflang output (metadata plumbing only).
- Analytics, error tracking, 2FA.

## 4. ADR status

| ADR | Decision | Status after 1A |
|---|---|---|
| 001 monolith | applied (Core/Domains structure) | ✅ |
| 002 SSR Blade + Livewire admin | SSR/Blade applied; Livewire lands with Blue Control | ✅ partial |
| 003 MySQL | applied (config default, composer exts, CI service) | ✅ |
| 004 animation | tokens + reduced-motion only, GSAP deferred | ✅ (as planned) |
| 005 three.js | deferred | ✅ (as planned) |
| 006 auth | admin guard + session auth applied; spatie RBAC deferred to Blue Control phase (schema not in 1A per brief) | ✅ partial |
| 007 media | media disk + validation boundary; medialibrary deferred | ✅ partial |
| 008 SEO | MetaResolver pattern applied; sitemap/redirects deferred | ✅ partial |
| 009 Vite/SCSS | applied (multi-entry, SCSS, no TS) | ✅ |
| 010 Blue Control | boundary + auth now; Livewire CRUD later | ✅ partial |
| 011 domain separation | boundaries created | ✅ |
| 012 leads | deferred (Lead domain) | ✅ as planned |
| 013 i18n/RTL | applied (locale registry, logical CSS, fa lang) | ✅ |
| 014 toolchain | updated: CI + WASM-lint verification path (see ADR update) | ✅ |
