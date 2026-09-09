# Blue Studio OS — System Architecture

**Status:** Accepted (Phase 0).
**Reader:** any developer implementing Blue Studio OS, and any reviewer checking
consistency with ADRs.

---

## 1. Summary

Blue Studio OS is a **modular monolith**:

- **Backend:** PHP 8.5+, Laravel 13.x, MySQL 8.x — one deployable application.
- **Rendering:** server-rendered Blade for the public site; Laravel Livewire inside
  Blue Control (the admin) for interactive CRUD; Alpine.js for small public
  progressive-enhancement islands.
- **Assets:** Vite + Vite plugin, dart-sass (SCSS) modules, ES modules.
- **Front-end animation:** modular GSAP + ScrollTrigger + Lenis, with Three.js as an
  isolated, lazily-loaded, fallback-safe layer.
- **Domains:** Products, Portfolio, Lab, Services, Insights (blog), Leads, Media,
  SEO, Content, Identity/Auth — organized as Laravel modules with dedicated
  `Models`, `Migrations`, `Actions/Services`, `Observers`, routes, and views.
- **Admin:** Blue Control — a Laravel Livewire application mounted under
  `/admin` with its own layout, auth guard, and RBAC.
- **No microservices. No API-first in v1.** Boundaries are shaped so an API and a
  client portal can be added without rewriting the system.

Rationale and alternatives are in
[`adr/0001-backend-architecture.md`](adr/0001-backend-architecture.md) and
[`adr/0002-frontend-rendering-strategy.md`](adr/0002-frontend-rendering-strategy.md).

---

## 2. Application boundaries (bounded domains)

```
┌────────────────────────────────────────────────────────────────────────────┐
│                            Blue Studio OS (Laravel)                         │
│                                                                            │
│  PUBLIC (Blade SSR)                          BLUE CONTROL (/admin, Livewire)│
│  ┌──────────────┐ ┌──────────────┐           ┌────────────────────────────┐ │
│  │ Domain:      │ │ Domain:      │           │  Dashboard                  │ │
│  │ Products     │ │ Portfolio    │           │  Products CRUD              │ │
│  │ Portfolio    │ │ (uses shared │           │  Portfolio / Lab / Services │ │
│  │ Services     │ │  media & seo)│           │  Blog (posts, cats, tags)   │ │
│  │ Insights     │ │              │           │  Leads + notes + status     │ │
│  │ Lab          │ │              │           │  Media library              │ │
│  │ Home/About   │ │              │           │  Users / Roles / Logs       │ │
│  │ StartProject │               │            │  Settings + SEO             │ │
│  └──────┬───────┘ └──────┬───────┘           └─────────────┬──────────────┘ │
│         │                │                                  │                │
│         ▼                ▼                                  ▼                │
│  ┌──────────────────────────────────────────────────────────────────────┐    │
│  │                    SHARED KERNEL (internal, not an MVC god layer)     │    │
│  │  MediaService · SeoService · SlugService · SettingsService            │    │
│  │  ActivityLogger · MetaResolver · CacheInvalidation · Formatters        │    │
│  └──────────────────────────────────────────────────────────────────────┘    │
│         │                │                                  │                │
│         ▼                ▼                                  ▼                │
│  ┌──────────────────────────────────────────────────────────────────────┐    │
│  │                          INFRASTRUCTURE                                │    │
│  │  Eloquent + MySQL 8 · Filesystem (local/disk/object) · Cache (file/… )│    │
│  │  Queue (database/sync v1) · Events · RateLimiter · Mail (log v1)       │    │
│  └──────────────────────────────────────────────────────────────────────┘    │
└────────────────────────────────────────────────────────────────────────────┘
```

**Rules:**

- Web controllers are thin: validate → authorize → call
  `Action`/`Service` → return view/redirect. **No business logic in controllers or
  views.**
- Domain "modules" are **namespace groups**, not separate packages (no premature
  multi-repo or package splitting). Each module owns its models, migrations,
  observers, actions, requests, routes, and views.
- Shared kernel provides cross-cutting services only (media, SEO, settings,
  slugs, logging, caching). It does **not** contain content business logic.
- Future API (Phase 5+) is a new inbound layer that calls the same
  Actions/Services — no logic duplication.

## 3. Request lifecycle (public)

```
Browser
  → nginx (TLS, compression, static caching, security headers)
    → Laravel public/index.php
      → bootstrap, service providers (domain + shared)
      → route model binding (slug → model)
      → FormRequest validation (POST only)
      → Policy authorization (admin routes only in v1)
      → Action/Service (write paths)
      → MetaResolver (SEO metadata per page)
      → Blade layout/render (design-system components)
      → Response (cookies, headers, cache-control)
```

Read paths are **fully server-rendered**. No client-side fetching of content in v1.

## 4. Directory layout (proposed, finalized in Phase 1 scaffold)

```
app/
├── Actions/                     # use-cases (write paths, one responsibility each)
│   ├── Products/
│   ├── Leads/
│   └── ...domain-agnostic
├── Http/
│   ├── Controllers/
│   │   ├── Web/{Home,Product,Portfolio,Lab,Service,Insight,Lead}.php
│   │   └── Admin/               # thin; Livewire handles most admin interactivity
│   ├── Middleware/
│   ├── Requests/
│   └── Resources/               # reserved for future API
├── Livewire/                    # Blue Control components (per domain)
│   ├── Admin/
│   │   ├── Dashboard/
│   │   ├── Products/
│   │   └── Leads/
├── Models/                      # all Eloquent models, per-domain namespaces
├── Observers/
├── Policies/
├── Providers/                   # App, domain providers
├── Services/                    # shared kernel: Media, Seo, Settings, Slug
├── Support/                     # value objects, formatters, casts, enums
├── View/
│   ├── Components/              # Blade components (design system)
│   └── Composers/
└── ...

bootstrap/
config/                          # app, media, seo, analytics, admin…
database/
├── factories/  migrations/  seeders/
docs/
resources/
├── css/                         # SCSS modules (tokens, base, components, utilities)
├── js/                          # ES modules: core, motion/, three/, admin/
├── lang/  views/  (blade)
routes/
├── web.php  admin.php  (channels, console)
tests/
├── Feature/  Unit/  Browser/  (Playwright)
storage/  (logs, media variants)  public/  (build, media symlink)
```

## 5. Configuration & environments

| Env | Purpose | DB | Cache/Queue | Media storage | Debug |
|---|---|---|---|---|---|
| `local` | developer machine | MySQL (or Herd-provided) | file | local | true |
| `development` (optional sandbox) | integration checks | MySQL | file | local | true |
| `staging` | pre-production QA, mirror of prod config | MySQL, separate DB | redis if available | local/disk | false |
| `production` | public | MySQL 8 (managed) | Redis (recommended) | S3-compatible object storage | false |

See [`deployment.md`](deployment.md) and
[`adr/0014-toolchain-and-environments.md`](adr/0014-toolchain-and-environments.md).

## 6. Internal service conventions

- **Actions** (`App\Actions\…`): single public method, named for the use-case
  (`CreateLead`, `PublishProduct`, `ImportMedia`). Return domain objects; throw
  domain exceptions. No HTTP knowledge.
- **Services** (shared kernel): stateless or lifecycle-managed, injected via
  container. Media/SEO/Settings/Slug are the only four in v1.
- **Requests** (FormRequest): validation + `authorize()`. Validation rules live here
  or in the Action if reused across HTTP and CLI.
- **Enums** (PHP backed enums) for every closed set: product status, demo type,
  lead status, post status, content workflow, media variant, locale.
- **Observers**: only for cross-cutting side effects (SEO snapshot, media cleanup,
  activity log, slug generation). Never for controller logic.
- **No magic strings/paths** in views: config files (`config/blue.php`-style sections
  per concern) are the source of truth.

## 7. Frontend architecture

- **Server-rendered Blade** with anonymous class components (design system), named
  slots for content, and `@props` with defaults. No Blade "logic" beyond control flow.
- **SCSS** with tokens-first architecture: `tokens` → `base` → `components` →
  `layouts` → `utilities`. RTL handled by tokens/CSS logical properties (see
  [`design-system.md`](design-system.md)).
- **JavaScript modules** importable by feature; no global barrel. Motion is its own
  module graph (see [`animation-system.md`](animation-system.md)); Three.js is a
  separate lazy entry (see [`threejs-architecture.md`](threejs-architecture.md)).
- **Alpine.js** for public micro-interactions (nav, dialogs, forms enhancement).
  TypeScript only in domains where it meaningfully improves reliability (three.js
  layer, admin utility modules) — see [`adr/0009-asset-build-tooling.md`](adr/0009-asset-build-tooling.md).

## 8. Dependency policy (Phase 0 decisions)

Locked or recommended dependencies and their reason:

| Dependency | Where | Reason |
|---|---|---|
| `laravel/framework` 13.x | backend | required baseline |
| `laravel/telescope` | **dev only** | local debugging; excluded from prod deps |
| `spatie/laravel-permission` | auth | battle-tested RBAC, keeps us from hand-rolling |
| `spatie/laravel-medialibrary` | media | variants/responsive generation on Filesystem abstraction (see ADR-007 note) |
| `spatie/laravel-sitemap` | SEO | publishes to storage, cacheable |
| `vite`, `laravel-vite-plugin`, `sass` | build | modern, Laravel-native asset pipeline |
| `gsap` (incl. ScrollTrigger) | motion | declarative, industry standard, tree-shaken |
| `lenis` | motion | smooth scroll, reduced-motion aware |
| `three` | 3D | lazy isolated module only |
| `alpinejs` | public islands | small, no framework migration |
| `@playwright/test` (dev) | E2E | critical flows |
| `vazirmatn` (npm) | typography | Persian webfont, self-hosted |

**Not installed in Phase 0.** No animation/3D/UI libraries are installed during
Phase 0 (rule 38). No payment, no portal, no API client, no Tailwind by default —
styling is SCSS token-driven because the design system is bespoke (Tailwind may be
reconsidered only if documented via ADR).

## 9. Data flow — write paths

```
[Admin form / Livewire]
   → FormRequest (validate)
   → Policy (authorize)                      [admin only]
   → Action (business rules, transactions)
   → Model events + Observers (SEO/media/log side effects)
   → Cache invalidation (sitemap, menus, listings)
   → Flash + redirect
```

```
[Public lead form]
   → Rate limiter (per IP + per email)
   → Honeypot/spam guard
   → FormRequest validation
   → CreateLead action (stores lead + source + fingerprint metadata)
   → Notification (mail log in v1)
   → Session "lead submitted" flag → thank-you state
```

## 10. Caching strategy (v1)

- **Views:** Blade fragment caching for heavy listings on public pages
  (products index, insights index) — invalidated on content status change.
- **Sitemap:** generated to storage and cached; regenerate on publish/unpublish.
- **Config:** `config:cache` in staging/production; **never** cache config in local.
- **Route cache** in production only.
- **No Redis requirement in v1** (file cache is acceptable); Redis is a deployment
  option documented in [`deployment.md`](deployment.md).

## 11. Observability

- Laravel logging channels: `stack` (daily) local/staging, single JSON channel prod.
- Domain events logged at `info` via ActivityLogger (see
  [`security.md`](security.md#logging-and-observability)).
- Auth events and admin mutations are `warning`/`info` with user + timestamp.
- Error reporting to a channel/file in v1; external error tracker is a documented
  future option (see [`roadmap.md`](roadmap.md) — Phase 5).

## 12. What is deliberately out of the v1 architecture

- Microservices / event bus / message queue (queue = `database`/`sync` only).
- Full REST API (boundaries prepared; API deferred).
- Payment processing, subscriptions, checkout.
- Client portal / project management.
- AI-powered features (deferred to a documented phase; nothing in the domain model
  blocks them — e.g., AI summarization of leads, content drafting).
- Multi-tenant tenancy (single studio site by definition).

## 13. Architecture checklist — resolved contradictions

| Question | Decision |
|---|---|
| Laravel or Node? | Laravel (baseline honored); Node only as build/tooling runtime |
| SSR or SPA? | SSR Blade; no SPA for public site |
| Admin as separate app? | No — Livewire module mounted at `/admin` (ADR-010) |
| Products/Portfolio same table? | No — separate domains (ADR-011) |
| Own payment? | No — external marketplace links only |
| Own RBAC? | No — spatie/permission (ADR-006) |
| Redis required? | No — optional in deployment |
| API first? | No — API-ready boundaries only |
| RTL later? | No — RTL-first tokens and layout now (ADR-013) |
