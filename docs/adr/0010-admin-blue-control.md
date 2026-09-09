# ADR-010 — Blue Control: Admin Implementation

- **Status:** Accepted
- **Date:** 2026-09-09
- **Supersedes:** none

## Context

Blue Control must let the studio operate content without touching code: rich CRUD,
media management, lead pipeline, SEO, settings, users. The decision must balance
development speed, control over design, maintenance cost, and the brief's
prohibition against over-engineering.

## Decision

- Blue Control is a **module inside the same Laravel app**, mounted at `/admin`,
  built with **Laravel Livewire** server-driven components.
- `admin` route file, `admin` guard, dedicated middleware stack (auth → verified →
  permission → activity-log → no-cache/noindex).
- Blue Control layout uses the **same design tokens** as the public site with an
  admin component layer (tables, forms, drawers) — a cohesive brand, not a foreign
  dashboard.
- Each module = Livewire list component + edit component + FormRequest + Action
  reuse (same use-cases as tests/CLI).
- Dashboard is a read-only query projection (no custom aggregation tables in v1).

## Alternatives considered

1. **Laravel Nova** — commercial license + opinionated UI; less control over premium
   design; not chosen.
2. **Filament** — excellent builder, but adds its own design system/widget model
   and often leads to admin "look" drift from the flagship public site; not chosen
   in Phase 0 (revisit only via ADR if development velocity demands).
3. **Separate admin SPA (Vue/React) + API** — rejected: double codebase, API
   required, higher cost; contradicts ADR-001/002.
4. **Livewire full-page + Jetstream** — Jetstream may scaffold auth; Livewire is the
   chosen interaction model regardless.

## Consequences

- ✅ Single codebase, shared tokens, fast admin development, no API for admin.
- ✅ All admin writes flow through domain actions → consistent audit/testing.
- ⚠️ Livewire component size discipline required (split per domain; no mega-forms).
- ⚠️ Chosen package set (Livewire + permission) must be reviewed at Phase 1 install.
