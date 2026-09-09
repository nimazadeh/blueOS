# Phase 2 Report — Blue Studio OS

## PHASE 2 STATUS: PASS WITH RISKS

Phase 2 (Blue Control & Business Domain Foundation) is implemented: Blue
Control admin with RBAC-protected management modules, five business domains
with complete schema and migrations, the Media attachment system, the lead
pipeline, database-driven public pages with SEO, audit logging and a full
Pest test suite. Static verification passes (112 PHP files lint-clean, Vite
build green); runtime verification remains blocked in this sandbox
(no PHP/MySQL runtime — same limitation as Phase 1A/1B), so the automated
suite is the verification gate. See Risks.

---

## 1. Implemented

### Blue Control admin (`/admin`)
- Full shell: sidebar navigation (Dashboard / Products / Portfolio /
  Services / Leads / Media / Settings / Activity), flash messages, pagination.
- Dashboard: published counts per domain + new-lead count + recent activity
  (efficient indexed queries, no analytics tables).
- CRUD foundation per module with publish workflows, status filters,
  ordering, forms backed by FormRequests + actions (see §4).

### RBAC foundation
- `roles`, `permissions`, `role_user`, `permission_role` tables and models.
- Roles **Owner / Admin / Editor** with permission matrix
  (`products.*`, `portfolio.*`, `services.*`, `leads.manage`, `media.manage`,
  `settings.manage`, `activity.view`).
- **Decision (documented): internal RBAC instead of spatie/laravel-permission.**
  The dependency could not be installed/verified in this sandbox, and the
  internal schema mirrors spatie's shape (swap is a migration + facade
  change — see `docs/admin.md`). This keeps the zero-unverifiable-dependency
  rule of Phases 1A/1B.
- Owner bypasses every check (`Gate::before`); policies guard model
  operations; `settings.manage` / `activity.view` gates guard the two
  model-less sections.

### Business domains (`app/Domains/*`)
- **Products**: `Product`, `ProductFeature`, `Technology` (+ pivot) with
  statuses draft/review/published/archived, type taxonomy, featured flag,
  ordered features, tech relations, media (cover + gallery), admin CRUD +
  publish workflow, public index/detail with SEO.
- **Portfolio**: `PortfolioProject` case studies (challenge / solution /
  results), featured flag, tech relations, media, publish workflow, public
  index/case-study page.
- **Services**: `Service` with icon, short+full description, `sort_order`
  ordering, publish control, public index/detail.
- **Leads**: `Lead` (pipeline `new` → `reviewing` → `contacted` →
  `proposal_sent` → `won`/`lost`), `LeadNote`, `LeadStatusHistory`
  (append-only). Public submission endpoint (throttled) + admin inbox
  (filter, detail, notes, status changes with history).
- **Media**: `Media` registry (morph attachments), completed
  `MediaService::attach/deleteFile`, admin library (upload/alt/delete),
  private `media` disk + public delivery route for published entities only.
- **Settings**: operator form (site identity, contact info, default SEO
  description) through the existing `SettingsService`; `MetaResolver` now
  reads `site.name` / `seo.default_description` settings before config
  defaults.

### Public website data connection
- Homepage now renders **published** products (featured first, 6), services
  (ordered, 6) and portfolio projects (featured first, 6) from the database;
  empty collections render design-system empty states (never fake content).
- New public pages: `/products`, `/products/{slug}`, `/portfolio`,
  `/portfolio/{slug}`, `/services`, `/services/{slug}` — all MetaResolver
  SEO (title/description/canonical/OG, og:image from covers).
- Real lead form on the homepage CTA (`POST /leads`, throttled, validated).
- Footer services now come from the database (view composer keeps queries
  out of views).

### SEO, activity, security
- MetaResolver wired into all public domain pages; canonical URLs from
  routes; OG types `article` for detail pages.
- ActivityLogger records: product/portfolio/service created/updated/deleted/
  status_changed, lead created/note/status_changed, settings updated.
- Every write path: FormRequest validation (statuses/types enumerated,
  MIME allowlists incl. SVG disabled everywhere, size/dimension caps),
  authorization policy checks, CSRF, auto-escaped output, `data-confirm`
  destructive actions, throttled public endpoints.

## 2. Database changes

New migrations (see `docs/database.md` §Phase 2):

| Migration | Creates |
|---|---|
| `0001…0100` | `roles`, `permissions`, `role_user`, `permission_role` |
| `0001…0101` | `media` (morph registry) |
| `0001…0102` | `products`, `product_features`, `technologies`, `product_technology` |
| `0001…0103` | `portfolio_projects`, `portfolio_project_technology` |
| `0001…0104` | `services` |
| `0001…0105` | `leads`, `lead_notes`, `lead_status_history` |

Config changes: `AUTH_GUARD` defaults to the `admin` guard (gate/can
consistency — documented in `docs/admin.md`); `media.disk` defaults to the
private `media` disk.

## 3. Admin features

- Products: list (status filter, pagination), create/edit (features repeater,
  technology checkboxes, cover + gallery upload), delete, publish/unpublish.
- Portfolio: list, create/edit (challenge/solution/results, technologies,
  media), delete, publish/unpublish.
- Services: list (ordered, status filter), create/edit (icon, sort_order),
  delete, publish/unpublish.
- Leads: list with status filter, detail (contact info, message, notes,
  status history), add note, change status.
- Media: upload (image-only, collection), alt text edit, delete.
- Settings: site identity/contact/SEO defaults (no secrets).
- Activity: paginated audit log.

## 4. Tests & static verification

| Check | Result |
|---|---|
| `npm run build` | **PASS** — Vite, 4 entries (see §5) |
| PHP-WASM `-l` syntax (PHP 8.5.10), 133 PHP files | **PASS — 0 failures** |
| Static gates (route names, views/`x-` components, controller classes/methods, `config('blue.*')` keys) | **PASS** |
| Language gate (`__()` keys in views + app vs `lang/en|fa`; en↔fa parity) | **PASS** — found and fixed `auth.failed` (missing `lang/*/auth.php`) |
| CSS audits (no physical left/right, no pure black, no raw hex outside tokens) | **PASS** |
| Pest feature tests (`tests/Feature/**`, **62** specs — **40** authored this phase) | **NOT EXECUTED** — no local PHP/Composer runtime |
| Playwright E2E + axe (9 specs) | **NOT EXECUTED** — browser binaries blocked |

Authored tests: Admin RBAC (owner bypass, editor limits, guest redirects),
Products (create/update/publish/authz/public), Portfolio (CRUD/workflow),
Leads (public submission/validation, admin status+notes, filtering), Media
(store/serve/deny/SVG rejection/alt/delete), Services (ordering/public),
plus the updated homepage suite. Exact run steps: see §Risks.

## 5. Performance measurements

| Asset | Raw | gzip |
|---|---|---|
| `app.css` | 29.90 kB | **6.19 kB** |
| `admin.css` | 32.45 kB | **6.44 kB** |
| `app.js` | 2.24 kB | 0.94 kB |
| `admin.js` (incl. admin-ui helpers) | 0.95 kB | 0.48 kB |
| `accessibility.js` chunk | 1.95 kB | 0.73 kB |

Runtime JS dependencies: **zero**. Eager loading (media on listings),
paginated admin tables (15–25/page), indexed status/published columns,
cover resolved from the loaded media relation (no N+1 per card).

## 6. Risks

1. **R1 — Runtime tests not executed locally.** Setup on any host with
   PHP ≥ 8.4 + Composer + MySQL 8: `docker compose up -d mysql` (or use
   `composer install` + local DB), then
   `cp .env.example .env && php artisan key:generate`,
   `php artisan migrate --seed`, `php artisan test`. The suite uses
   in-memory SQLite by default and MySQL in CI.
2. **R2 — CI still not pushed** (GitHub App token lacks `workflows` scope;
   verified twice in Phase 1A). `.github/workflows/ci.yml` remains in the
   workspace; owner action required (see `docs/phase-1a-report.md` §6).
3. **R3 — Browser verification pending** (Playwright/axe authored, not run;
   `npx playwright install chromium` required on a provisioned machine).
4. **R4 — Content localization deferred.** Phase 0 planned JSON/i18n content
   columns; Phase 2 implements the brief's single-language columns. Adding
   `{en,fa}` JSON twins later is a documented, non-breaking migration path
   (`docs/database.md` §Phase 2 notes).
5. **R5 — Pest suite never executed.** Static gates pass (lint, routes,
   views/`x-` components, controllers, config, language parity) but the
   suite has never run; first execution should be `php artisan test` on a
   provisioned host.

## 7. Next recommended phase

**PHASE 3 — PREMIUM EXPERIENCE LAYER** (GSAP/Three.js enhancement,
advanced product showcase, demo gates, performance optimization).

*Phase 3 is NOT started — delivered as a recommendation only.*
