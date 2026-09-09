# ADR-011 — Products / Portfolio / Lab Domain Separation

- **Status:** Accepted
- **Date:** 2026-09-09
- **Supersedes:** none

## Context

The brief mandates: Products (owned/commercial), Portfolio (client/custom work), and
Lab (experimental/R&D) are **conceptually distinct**; "do not create one giant
generic projects entity unless the architecture explicitly justifies it". They
share media and technologies but differ in fields, CTAs, statuses, and SEO.

## Decision

**Three separate bounded domains** — `products`, `portfolio_projects`,
`lab_experiments` — with distinct tables, models, controllers, routes, URLs,
views, and Blue Control sections. Shared **global** `technologies` master and
**typed** `tags` master with per-domain joins. `media` is shared via the morph
registry. No inheritance, no "type" discriminator column.

| Domain | URL | Own fields | Status enum |
|---|---|---|---|
| Product | `/products/{slug}` | price, marketplace/purchase, demo (type/status/url), version/release, features, case study | draft / published / archived |
| Portfolio | `/portfolio/{slug}` | client, challenge/context/approach/solution/architecture/results, project type, dates | draft / published / archived |
| Lab | `/lab/{slug}` | hypothesis/findings/approach, experiment state, repository/live URL | draft / visible / archived |

## Alternatives considered

1. **Single `projects` table + type enum** — rejected: mixes commerce fields,
   demo semantics, case-study structure; forces nullable unions and weak
   validation; future product commerce would corrupt portfolio data.
2. **Polymorphic single table** — same issues + type-unsafe relations.
3. **Three tables sharing an abstract base/interface** — rejected: PHP/BLade
   inheritance adds abstraction without benefit; duplication is declarative and
   cheap, maintenance is explicit.
4. **Lab as a sub-type of products** — rejected: Lab has no commercial semantics;
   would leak pricing fields into experiments.

## Consequences

- ✅ Clear content model, clean admin, distinct SEO and CTAs, no data corruption.
- ✅ Future commerce/product evolution doesn't touch portfolio/lab.
- ⚠️ Some model/view duplication across domains — accepted as explicit clarity
  (reuse via blade components, not inheritance).
- ⚠️ Admin has three CRUD modules — acknowledged; Blue Control design supports
  module extension.
