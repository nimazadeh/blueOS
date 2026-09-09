# Blue Studio OS — Documentation

This directory is the **single source of truth** for all architectural decisions for
Blue Studio OS. Phase 0 locked each major decision in writing before any code was
written.

## Reading order

1. [`environment.md`](environment.md) — what was actually discovered (toolchain, network, repo)
2. [`vision.md`](vision.md) — what Blue Studio OS is, for whom, and why
3. [`architecture.md`](architecture.md) — the system blueprint (stack, boundaries, code layout)
4. [`architecture-implementation.md`](architecture-implementation.md) — **what is actually implemented** (Phase 1A structure, conventions, deferred items)
5. [`development.md`](development.md) — local setup (Docker/native), commands, env vars
6. [`information-architecture.md`](information-architecture.md) — public site IA, URLs, funnels
7. [`database.md`](database.md) — conceptual + logical schema
8. [`design-system.md`](design-system.md) — tokens and RTL-first visual foundation
9. [`media-architecture.md`](media-architecture.md) — assets, variants, storage abstraction
10. [`admin-blue-control.md`](admin-blue-control.md) — the Blue Control CMS architecture
11. [`lead-management.md`](lead-management.md) — lead capture and lifecycle
12. [`seo.md`](seo.md) — technical SEO model
13. [`security.md`](security.md) — security foundation
14. [`animation-system.md`](animation-system.md) + [`threejs-architecture.md`](threejs-architecture.md) — motion & 3D
15. [`accessibility.md`](accessibility.md) + [`performance.md`](performance.md) — quality gates
16. [`testing.md`](testing.md) + [`qa.md`](qa.md) — how quality is proven
17. [`deployment.md`](deployment.md) — environments and release path
18. [`roadmap.md`](roadmap.md) — what ships in v1 and what does not
19. [`phase-0-report.md`](phase-0-report.md) — Phase 0 final report
20. [`phase-1a-report.md`](phase-1a-report.md) — Phase 1A final report (current)

## Architecture Decision Records (ADRs)

Numbered records in [`adr/`](adr/). Each contains Context / Decision / Alternatives
considered / Consequences. They are immutable once accepted; supersede with a new ADR.

| ADR | Decision |
|---|---|
| [ADR-001](adr/0001-backend-architecture.md) | Backend architecture — modular monolith on Laravel |
| [ADR-002](adr/0002-frontend-rendering-strategy.md) | Frontend rendering — Blade SSR + progressive islands; Livewire for Blue Control |
| [ADR-003](adr/0003-database-strategy.md) | Database strategy — MySQL 8, Eloquent, migrations, single schema |
| [ADR-004](adr/0004-animation-architecture.md) | Animation architecture — modular GSAP/ScrollTrigger/Lenis, motion as system |
| [ADR-005](adr/0005-threejs-integration-strategy.md) | Three.js integration — isolated lazy modules, graceful fallback |
| [ADR-006](adr/0006-authentication-strategy.md) | Authentication & authorization — session auth + RBAC (spatie/permission) |
| [ADR-007](adr/0007-media-storage-strategy.md) | Media storage — internal MediaService over Laravel Filesystem |
| [ADR-008](adr/0008-seo-strategy.md) | SEO — server-rendered metadata, morph-based SEO records, cached sitemap |
| [ADR-009](adr/0009-asset-build-tooling.md) | Asset build — Vite + SCSS, TypeScript only where it pays |
| [ADR-010](adr/0010-admin-blue-control.md) | Blue Control — Laravel Livewire admin, not a separate app |
| [ADR-011](adr/0011-domain-separation.md) | Products / Portfolio / Lab — three separate domains |
| [ADR-012](adr/0012-lead-management.md) | Lead management — first-class lead domain with status model |
| [ADR-013](adr/0013-i18n-rtl.md) | Internationalization — locale-aware URLs, JSON translation columns, RTL-first tokens |
| [ADR-014](adr/0014-toolchain-and-environments.md) | Toolchain & environments — discovered sandbox gap and provisioning strategy |

## Conventions across this documentation

- "v1" means the first public release described in [`roadmap.md`](roadmap.md).
- Locale keys: `en` (English), `fa` (Persian). `APP_DEFAULT_LOCALE` decides the
  unprefixed URL set; see [`information-architecture.md`](information-architecture.md#localization-and-urls).
- Every schema reference here is authoritative for Phase 1 migrations; if a change
  is needed later, update this doc **and** add/update an ADR.

## Phase 2 (implemented)

- [`admin.md`](admin.md) — Blue Control: routes, RBAC, sections, safety.
- [`domains.md`](domains.md) — products/portfolio/services/leads/media
  domain contracts and conventions.
- [`media.md`](media.md) — media system implementation (storage, delivery,
  validation).
- [`database.md`](database.md) — §11: Phase 2 implemented schema.
- [`phase-2-report.md`](phase-2-report.md) — Phase 2 status report.
