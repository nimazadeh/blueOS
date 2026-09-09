# ADR-003 — Database Strategy

- **Status:** Accepted
- **Date:** 2026-09-09
- **Supersedes:** none

## Context

The platform needs a normalized, schema-first content + operational model that a
Laravel developer can reason about; it must support locales, strict statuses,
auditability, and future migration without lock-in. Brief baseline: MySQL.

## Decision

- **MySQL 8.x** (InnoDB, `utf8mb4`, `utf8mb4_unicode_ci`), Laravel migrations +
  Eloquent, single schema, one database per environment.
- **Logical schema per [`../database.md`](../database.md)** — explicit domain tables;
  per-resource join tables; central `media` registry (morph), `seo_meta` (morph),
  `redirects`, `settings`, `activity_logs`.
- **Localized fields:** JSON columns keyed by locale (`en`, `fa`) with a single
  `Translation` accessor layer; slugs remain single strings.
- **Statuses:** PHP backed enums stored as strings; lifecycle enforced by domain
  services (LeadStatusService, workflow rules).
- Soft deletes on content; audit tables immutable; no triggers (kept in app layer).
- Migrations are the source of schema truth; no manual schema editing.

## Alternatives considered

1. **PostgreSQL** — capable, but brief fixes MySQL; no reason to deviate; MySQL 8
   JSON/features suffice.
2. **Normalized translations tables** — avoided in v1: JSON columns are sufficient
   for two locales and reduce join/query complexity; conversion documented if a
   future search feature requires it (ADR if it happens).
3. **Single `projects` mega-table** — rejected (ADR-011).
4. **Eloquent-free raw SQL / repository layer** — rejected: adds ceremony; Eloquent
   with explicit Action boundaries is enough.
5. **SQLite for tests** — rejected: schema differences hide MySQL issues; CI uses
   MySQL 8 service.

## Consequences

- ✅ One schema, predictable queries; indexes defined upfront for slugs/statuses.
- ✅ Locale-independent core model; RTL content is a column, not a separate system.
- ⚠️ JSON locale columns less queryable — acceptable for two locales; search phase
  will revisit.
- ⚠️ App-layer status enforcement requires tests (covered in testing.md).
- ⚠️ Migration discipline required (documented in deployment.md rollback plan).
