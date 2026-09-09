# ADR-001 — Backend Architecture

- **Status:** Accepted
- **Date:** 2026-09-09
- **Supersedes:** none

## Context

Blue Studio OS must serve a public content site (products, portfolio, lab, services,
blog), a lead engine, and an admin system; it must remain maintainable by a
professional Laravel developer; and it must not over-engineer with microservices or
enterprise layers. The brief fixes the baseline: PHP + Laravel + MySQL.

## Decision

A **modular monolith** on Laravel 13.x (PHP 8.5, MySQL 8):

- One deployable application; one schema; one codebase.
- Bounded domains (Products, Portfolio, Lab, Services, Insights, Leads, Media, SEO,
  Identity) as namespaced modules inside the app — each owning its models,
  migrations, observers, actions, requests, routes, views.
- Thin HTTP controllers → `Action`/`Service` use-cases; no business logic in
  controllers or Blade views.
- Shared kernel: MediaService, Seo/MetaResolver, SlugService, SettingsService,
  ActivityLogger (cross-cutting only).
- Future API is an inbound layer over the same Actions (no rewrite).

## Alternatives considered

1. **Microservices per domain** — rejected: operational complexity, no team/deploy
   scale need, contradicts maintainability.
2. **Laravel packages per domain** — rejected for v1: package boundaries add
   friction without benefit on a single-tenant single-studio site.
3. **Laravel Nova / Filament for admin** — not chosen for admin (see ADR-010).
4. **Node/Next.js stack** — rejected by brief baseline; SSR Blade chosen (ADR-002).

## Consequences

- ✅ One deploy, one CI, one schema — cheap to operate.
- ✅ Clear domain boundaries make future extraction possible without rewrite.
- ⚠️ Requires discipline: modules must not leak (no cross-domain model access);
  enforced in code review + architecture tests (Phase 1).
- ⚠️ Monolith can grow; mitigated by job queues/events only when needed.
