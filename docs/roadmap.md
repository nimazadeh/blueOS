# Blue Studio OS — Roadmap

**Status:** Accepted (Phase 0). This is the planned phase order. **Phases beyond
the current instruction are not started automatically.**

---

## Phase map

```
Phase 0 — Foundation, Discovery & Architecture   ✅ (current, complete)
Phase 1 — Design System & Core Foundation        ⏭ next (not started)
Phase 2 — Public Experience & Product System
Phase 3 — Blue Control CMS
Phase 4 — Content Growth & Insights Engine
Phase 5 — Analytics, API & Ops Hardening
Phase 6 — AI-powered Features (stretch)
Phase 7 — Client Portal & Project Management (stretch)
```

## v1 scope (definitive, Phase 1–3)

| Area | In v1 |
|---|---|
| Public site | Home, Products (index/detail), Portfolio (index/case study), Lab, Services, Insights (index/detail), About, Start a Project, Contact, legal/error pages |
| Product model | categories, features, tags, technologies, media, demo (external/internal + statuses), marketplace/purchase URL, price display, version/release, featured |
| Portfolio model | challenge/approach/solution/results case-study fields, categories, tech, media |
| Lab model | experiment states, tags, media, repository/live URL |
| Services model | outcome-led, categories, deliverables |
| Insights model | posts with draft/review/published, categories/tags, author (real), SEO |
| Leads | full capture + Blue Control pipeline + notes + history |
| Media | upload, variants, responsive images, replace/delete/restore, MediaManager |
| SEO | MetaResolver, seo_meta, hreflang, structured data, sitemap per locale, robots, redirects |
| Admin | Blue Control: dashboard (required data), all CRUD, media, leads, settings, users/roles, SEO |
| I18n | en/fa locales, RTL-first design, locale URLs, hreflang |
| Motion | tokens, reduced-motion, reveals, micro-interactions, drawer/dialog, gallery |
| Quality | a11y (WCAG 2.2 AA), performance budgets, tests (unit/feature/E2E), CI |

## Explicitly NOT in v1 (documented deferrals)

| Item | Reason | Phase |
|---|---|---|
| In-site payment processing | external marketplaces only | never unless requested |
| Client portal | needs product roadmap confirmation | 7 |
| Full public API | no consumer yet | 5 |
| Microservices / event bus | premature | — |
| 2FA, passwordless | schema-ready, later | 3 |
| Custom RBAC UI | spatie + roles seeds only | 3+ |
| Full-text search | post-CMS | 4 |
| Third-party analytics | consent + ownership review | 5 |
| AI features | not product-defined yet | 6 |
| RSS, newsletter | after content engine | 4 |
| AVIF generation | toolchain validation | 2 |
| Visual regression suites | after components stabilize | 3 |
| Load testing | after traffic evidence | 5 |
| External error tracker | cost/benefit later | 5 |
| DMARC/email deliverability | when mail provider added | 2 |
| WhatsApp/Telegram lead notifications | locality decision | 2 candidate |
| Persian social share meta nuances | with i18n polish | 3 |

## Phase 1 — Design System & Core Foundation (recommended exact scope)

> Do NOT begin until the next phase instruction. Scope below is what Phase 1 will
> implement when instructed.

1. **Provision toolchain** (blocking prerequisite): PHP 8.5 + Composer + MySQL 8 on
   dev machine or staging VM (Herd/Docker); resolve sandbox runtime gap
   ([`environment.md`](environment.md)).
2. **Scaffold Laravel 13** with repository structure per
   [`architecture.md`](architecture.md#4-directory-layout); `.gitignore` config.
3. **Design tokens** (`tokens/` SCSS): colors, spacing, radius, shadows, typography,
   motion per [`design-system.md`](design-system.md); **RTL verified from first token**.
4. **Base design system components:** Container/Grid/Section/Card/Button/Badge/
   Eyebrow/Field/Input/Select/Dialog/Drawer/MediaImage + focus/a11y
   (`aria`, keyboard, `prefers-reduced-motion`).
5. **Typography setup:** Vazirmatn (fa) + Latin stack; `font-display: swap`;
   subset preloading; locale switching boots.
6. **Core layouts:** public layout (header + nav + drawer + footer), error layouts,
   locale wiring (`en`/`fa`, dir switch, lang attr, hreflang scaffolding).
7. **Home page v1:** hero (static first — no 3D), trust strip, products preview,
   services preview, portfolio select, lab tease, process, CTA, footer — all SSR,
   with real content slots; **no fabricated content**.
8. **Foundation services:** SettingsService, SlugService, MetaResolver (head meta
   component), MediaService core (validation + variants), CacheInvalidation events.
9. **Core models/migrations:** users/roles (spatie), settings, media, seo_meta,
   redirects, activity_logs, plus the **base tables of products/portfolio/lab/
   services/posts/leads** per [`database.md`](database.md) (full CRUD UIs land in
   Phases 2–3).
10. **Auth foundation:** admin guard, login (rate-limited), middleware/permissions,
    Blue Control layout skeleton + placeholder dashboard.
11. **Motion foundation:** `prefers-reduced-motion` baseline; Reveal via
    IntersectionObserver (GSAP deferred to Phase 2); micro-interaction CSS.
12. **Testing foundation:** Pest + feature test setup (MySQL CI service), Playwright
    scaffold (provisioned env), initial unit tests (SlugService, MetaResolver,
    Settings, Media validation), a11y axe smoke.
13. **Deployment scaffold:** local + staging Docker Compose (php-fpm/mysql/nginx),
    GitHub Actions skeleton, env example, deploy docs per
    [`deployment.md`](deployment.md).

**Phase 1 exit criteria:** app boots on PHP/MySQL; tokens + core components in use;
home renders without layout errors; auth login works; test suite green; no
placeholder/fake content; performance budgets measured.

## Content model boundary (final)

**Database-driven (content ops):** products, portfolio, lab, services, posts,
categories/tags, media, leads/notes/history, settings, seo_meta/redirects,
users/roles, activity logs.

**Static/system-driven (code):** routes, Blade component structure, design tokens,
motion/3D architecture, forms/validation logic, deployment, tests, config defaults.

**Rule:** if the studio needs to edit it from Blue Control for normal operations,
it is data; otherwise it is code.

## Future-phase gates

- Phase 2 cannot start until Phase 1 acceptance criteria met.
- Phase 3 (Blue Control full CRUD) depends on Phase 1 base tables + Phase 2 public
  data model validation.
- 3D modules enter only after Phase 2 motion architecture is live and measured;
  each module requires a designed static fallback first.
- No new major dependency without an ADR (rule 40).
