# Phase 0 Report — Blue Studio OS

**Phase:** 0 — Foundation, Discovery & Architecture
**Date:** 2026-09-09
**Author:** Lead architecture (Phase 0 role)
**Repository:** `nimazadeh/blueOS` — branch `arena/01a086c9-blueos`

---

## 1. Executive Summary

Blue Studio OS is a **modular Laravel monolith** (PHP 8.5+, Laravel 13, MySQL 8)
that serves four jobs from one platform: present the studio, showcase original
products, expose product demos/commercial info, and generate/manage client leads.
The public site is **Blade SSR** (SEO- and performance-first, works without JS),
admin is **Blue Control** — a Livewire module at `/admin` in the same app — and
frontend assets run through **Vite + SCSS + ES modules** with GSAP/ScrollTrigger/
Lenis in a modular motion layer and **Three.js as an isolated, lazy, fallback-safe
capability** (not a page requirement).

Products, Portfolio, and Lab are **separate bounded domains** (no generic
"projects" table). Leads are a first-class domain with a strict status lifecycle
and audit trail. Persian/RTL is a **first-class architectural property** (logical
CSS properties, Vazirmatn, locale-prefixed URLs, JSON-localized content), not a
flip. SEO, media, security, accessibility, performance budgets, testing, and QA
are defined as system constraints before implementation starts.

**The repository is greenfield** — only a 9-byte `README.md` and git metadata.
Nothing was "migrated" or "fixed"; Phase 0 delivers documentation only, per the
brief.

## 2. Environment (discovered, not assumed)

| Tool | Discovered |
|---|---|
| OS | Debian 12 (bookworm), x86_64, 2 vCPU, 3.8 GiB RAM, ~20 GB free |
| Git | ✅ 2.39.5 |
| Node.js / npm | ✅ 22.22.3 / 10.9.8 |
| PHP | ❌ not installed |
| Composer | ❌ not installed |
| MySQL / MariaDB | ❌ not installed |
| Docker | ❌ not installed |
| Playwright CLI | ⚠️ 1.63.0 CLI present — **browser binaries not installed** |
| Network | ✅ npm registry, github.com, api.github.com · ❌ apt (deb.debian.org), Packagist, GitHub release assets, cdn.playwright.dev |

**Verified current versions** (from live registries, 2026-09-09): Laravel
framework 13.31.0 (PHP ≥ 8.3), Composer 2.10.3, PHP 8.5.10 stable, Vite 8.2.2,
GSAP 3.15.0, Lenis 1.3.26, Three 0.186.0, Vazirmatn 33.0.3, Playwright 1.63.0.

**Critical finding:** the sandbox cannot run the requested stack today — PHP,
Composer, MySQL are absent and the package sources required to install them are
unreachable. Full record: [`environment.md`](environment.md);
decision: [`adr/0014-toolchain-and-environments.md`](adr/0014-toolchain-and-environments.md).

## 3. Final Architecture

- **Backend:** Laravel 13 (PHP 8.5) modular monolith; domains as namespaced modules;
  thin controllers → Actions/Services; shared kernel (Media, SEO/Meta, Slug,
  Settings, Activity).
- **Frontend:** Blade SSR + reusable class components; Vite multi-entry; SCSS
  token system; Alpine islands; GSAP/ScrollTrigger/Lenis motion layer; Three.js
  lazy modules (ADR-005).
- **Admin:** Blue Control — Livewire at `/admin`, separate guard/route group,
  RBAC via spatie/permission, same design tokens (ADR-010).
- **Data:** MySQL 8, single schema, JSON locale columns, morph media/SEO registries,
  strict enums for statuses, soft deletes on content, immutable audit tables
  (ADR-003).
- **APIs:** none in v1; boundaries prepared (Actions callable by future API layer).
- **Deployment:** local/staging/prod per [`deployment.md`](deployment.md); provider
  choice open (PaaS, VPS, or Docker Compose — Phase 1 with owner).

## 4. Domain Model (core entities)

```
Identity:   User —Role—Permission (spatie), ActivityLog
Products:   Product ▸ Category · Features · Tags(join) · Technologies(join) · Media
            + Demo (type/status/url) + Commercial (price display, marketplace,
              purchase, docs, repo, version, release)
Portfolio:  PortfolioProject ▸ ProjectCategory · Technologies(join) · Media
            + Challenge/Context/Approach/Solution/Architecture/Results
Lab:        LabExperiment ▸ Tags(join) · Media
            + Hypothesis/Findings/Approach + ExperimentState + repo/live URLs
Services:   Service ▸ ServiceCategory · Media (outcome-led)
Insights:   Post ▸ PostCategory · Tags(join) · Media · Author(real User)
Leads:      Lead ▸ LeadNotes ▸ LeadStatusHistory (8-status lifecycle)
Media:      central morph registry + variants (filesystem/object storage)
Platform:   SeoMeta (morph per locale) · Redirects(301) · Settings · ActivityLogs
```

Full schema with keys/indexes: [`database.md`](database.md).

## 5. Public Website (IA)

`/` Home · `/products/{slug}` · `/portfolio/{slug}` · `/lab/{slug}` ·
`/services/{slug}` · `/insights/{slug}` · `/about` · `/start-a-project` ·
`/contact` · `/privacy` · error pages. Locale-prefixed mirror under `/fa/…`
(default locale unprefixed; hreflang + canonical per locale). Slug-only URLs,
no public IDs. Full IA + funnels + page blueprints:
[`information-architecture.md`](information-architecture.md). Funnels:
Custom Project / Product / Portfolio Trust (documented §7 of IA).

## 6. Admin — Blue Control

Blueprint Control sections (Dashboard, Products, Portfolio, Lab, Services,
Insights, Categories, Leads, Media, SEO, Settings, Users, Activity) with dashboard
data requirements (no premature aggregation tables), content workflows
(draft→review→published→archived per type), lead pipeline, media manager, audit
logging, role seeds (owner now; admin/editor/staff future).
[`admin-blue-control.md`](admin-blue-control.md).

## 7. Design System

Token-first: deep-navy surfaces (never pure black), blue-gray cards, bright-blue
accent, soft-white text; logical-property-based spacing/radius/shadow/motion
tokens; Vazirmatn for Persian + self-hosted Latin stack; RTL-native from token #1;
WCAG-contrast-checked tokens; restrained premium direction (no clutter/glow/gradients).
[`design-system.md`](design-system.md).

## 8. Motion & 3D

- Motion = system: MotionManager registry, module-per-feature, lifecycle cleanup,
  `prefers-reduced-motion` gates, ≤40 kB initial motion JS, opacity/transform only.
- Three.js = isolated lazy modules (DigitalCore / ProductScene / InteractiveShowcase /
  LabScene as concepts), capability tiers, designed static fallback, destroy/cleanup,
  zero critical-path cost. **Nothing installed in Phase 0.**
[`animation-system.md`](animation-system.md) · [`threejs-architecture.md`](threejs-architecture.md).

## 9. Security

Session auth (admin guard, Argon2id, rate-limited login + lockout logging), RBAC
(spatie/permission, route + action checks), CSRF, FormRequest validation, Eloquent
binding (SQLi), Blade escaping + rich-text sanitization (XSS), strict upload
validation (SVG disabled by default), honeypot/rate-limit on public forms, HMAC
IP hashing, secure headers + CSP with demo allowlist, no secrets in source,
activity/auth audit logs, admin no-store/noindex — detailed in
[`security.md`](security.md).

## 10. SEO

MetaResolver head-meta system (title/desc/canonical/OG/Twitter/robots) with
`seo_meta` morph records per locale; honest structured data (Organization/Product/
Service/Article/BreadcrumbList — **no fabricated ratings or metrics**); per-locale
cached sitemaps + robots; 301 redirect system with slug-change automation; clean
slug URLs; noindex rules for drafts/admin/filters. [`seo.md`](seo.md).

## 11. Testing

Three layers: unit (Actions/Services/value objects — SlugService, LeadStatusService,
MetaResolver, MediaService, DemoLink), feature (routes/forms/authz/content
lifecycle/SEO/media on MySQL 8 CI), E2E (Playwright: 11 critical journeys incl.
lead flow, admin CRUD, RTL, reduced-motion, mobile, axe a11y). CI gates: Pint,
PHPStan, Pest (≥80% domain coverage), npm audit/composer audit, Playwright, axe,
Lighthouse budgets. [`testing.md`](testing.md) · [`qa.md`](qa.md).

## 12. Known Risks (tracked, with mitigations)

| # | Risk | Severity | Mitigation |
|---|---|---|---|
| R1 | **Sandbox cannot run PHP/MySQL today** (no PHP/Composer/MySQL; apt + Packagist blocked) — Phase 1 code cannot execute here | **High** | Provision dev machine/staging VM (Herd or Docker Compose) before Phase 1 coding; sandbox serves as docs/frontend-asset workspace. ADR-014. |
| R2 | Browser E2E cannot run in sandbox (no browser binaries, `cdn.playwright.dev` blocked) | Medium | Run Playwright on provisioned dev/CI; sandbox supports authoring tests only. |
| R3 | Single-operator content model; editor/staff roles deferred | Low | Roles + permissions schema seeded; 2FA planned Phase 3; audit logs from day 1. |
| R4 | Laravel 13 is current major — framework churn during early phases | Low | Pin via lockfile + documented upgrade strategy (ADR-001/003). |
| R5 | JSON locale columns limit SQL-level translation search | Low | Revisit with a dedicated ADR in Phase 4 (search) if needed; accessor layer isolates change. |
| R6 | Medialibrary (or chosen media impl) must be validated against variant/WebP needs | Medium | Service seam isolates implementation; replace generator behind same API. |
| R7 | Fictional-content temptation for demo purposes | Low | Factories clearly Test-scoped; production seeder creates no business content; no fake clients/stats (brief rule 38). |

## 13. Assumptions (documented decisions)

1. Bilingual `en` + `fa` in v1; `en` default, `fa` prefixed; other locales via same
   mechanism.
2. Products monetize through **external marketplaces**; no in-site payments unless a
   future phase explicitly introduces it.
3. Blue Control operator = studio owner; future roles are schema-ready only.
4. Provider/provisioning final choice is a Phase 1 owner decision (PaaS vs VPS —
   architecture supports both).
5. Real content only — no fabricated clients, portfolio items, or statistics will be
   seeded into production.
6. Package set is deliberate; any addition needs justification and an ADR.
7. Persian content will be authored by a real Persian-speaking editor; English is
   the fallback locale.

## 14. Deferred Decisions (intentionally postponed)

2FA mechanism (Phase 3), email/messaging provider + deliverability (Phase 2),
external CRM/notification integration (Phase 5), analytics storage/provider
(Phase 5), full-text search (Phase 4), AVIF generation (Phase 2), visual regression
runner (Phase 3), error tracker (Phase 5), newsletter/RSS (Phase 4), client portal
(Phase 7), AI features (Phase 6), Tailwind reconsideration (only via ADR), load
testing (Phase 5). All are recorded in [`roadmap.md`](roadmap.md).

## 15. Phase 1 Readiness

**Ready for Phase 1 implementation planning — with one blocking prerequisite.**

- ✅ Architecture, schema, UX tokens, motion/3D, security, SEO, a11y, performance,
  testing, QA, deployment, and roadmap are fully documented; no unresolved
  architectural contradiction remains (documents cross-checked; ADRs cover each
  major decision).
- ✅ Phase 1 scope is defined in [`roadmap.md`](roadmap.md) (design system & core
  foundation).
- ⛔ **Blocking prerequisite:** a runnable PHP 8.5 + Composer + MySQL 8 environment
  must be provisioned (dev machine or staging VM) before Phase 1 code can be
  authored/executed. Until then, Phase 1 can only be planned or its frontend/
  documentation parts authored in this sandbox.

---

```
PHASE 0 STATUS:
PASS WITH RISKS
```

**Reason:** all acceptance criteria (§42 of the brief) are met — environment and
repository are known and recorded; product vision/audiences/IA/models/leads/admin/
database/security/SEO/media/motion/3D/RTL/a11y/performance/testing/QA/deployment/
roadmap are documented with ADRs for every major decision; no contradiction remains.
The **only** significant risk is environmental: the requested runtime stack is not
executable in the sandbox (R1/R2), which does not affect the Phase 0 deliverable but
**must be resolved before Phase 1 implementation code** and is honestly reported,
not hidden.

## Recommended Phase 1 scope (do not start automatically)

**PHASE 1 — DESIGN SYSTEM & CORE FOUNDATION**

1. Provision PHP 8.5.10 + Composer 2.10.3 + MySQL 8 (local Herd or `deploy/docker-compose.yml`) — prerequisite.
2. Scaffold Laravel 13 skeleton with architecture directory layout.
3. Implement design tokens (colors/spacing/radius/typography/shadow/motion) as SCSS; verify RTL from first token; install Vazirmatn + Latin font pipeline.
4. Build base design-system components: Container/Grid/Section/Card/Button/Badge/Eyebrow/Field/Input/Select/Dialog/Drawer/MediaImage with a11y + focus + reduced-motion.
5. Implement public layout (header/nav/drawer/footer), locale bootstrapping (`en`/`fa`, `dir`, `lang`, hreflang scaffolding), error/legal pages.
6. Build **Home v1** — static hero (no 3D), trust strip, products preview, services preview, portfolio select, lab tease, process, CTA — with real content slots, zero fabricated content.
7. Implement foundation services: SettingsService, SlugService, MetaResolver/HeadMeta, MediaService (validation + variants), cache/event invalidation.
8. Implement core models + migrations: users/roles (spatie), settings, media, seo_meta, redirects, activity_logs, and base product/portfolio/lab/service/post/lead tables.
9. Implement admin auth (guard, rate-limited login, permissions middleware) + Blue Control layout skeleton + placeholder dashboard with real queryable metrics.
10. Motion foundation: reduced-motion baseline CSS, Reveal via IntersectionObserver (GSAP deferred to Phase 2), micro-interaction CSS.
11. Testing foundation: Pest + MySQL CI service, Playwright scaffold (provisioned env), initial unit tests (Slug/Meta/Settings/Media), axe smoke; CI skeleton.
12. Deployment scaffold: Docker Compose (php-fpm/nginx/mysql), env example, GitHub Actions skeleton, ops doc.

**Phase 1 exit gates:** app boots on PHP+MySQL; tokens/core components in use; home renders cleanly SSR; admin login + auth works; tests green; no placeholder content; performance budget measured (Lighthouse baseline).

*Awaiting the next phase instruction. No Phase 1 implementation has been started.*
