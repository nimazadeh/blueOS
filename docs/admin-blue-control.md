# Blue Control — Admin System Architecture

**Status:** Accepted (Phase 0). Blue Control is the internal operations system for
Blue Studio OS, mounted under `/admin` in the same Laravel application.

---

## 1. Identity & boundary

- **Not a separate app.** Blue Control lives inside the Blue Studio OS monolith
  (see [`adr/0010-admin-blue-control.md`](adr/0010-admin-blue-control.md)).
- Route group: `/admin` (Laravel `admin.php` route file), guard `admin`,
  middleware: `auth:admin` → `verified` → `role/permission` → `log.activity`
  (see [`security.md`](security.md)).
- UI: server-rendered Blade layout + Livewire components. No SPA. No separate JS
  framework. The admin is internal and must not create public-site maintenance cost.

## 2. Section map (v1)

```
/admin                     Dashboard (metrics + shortcuts + recent activity)
/admin/products            Products list/editor (incl. demo, commercial, SEO fields)
/admin/portfolio           Portfolio CRUD
/admin/lab                 Lab experiments CRUD
/admin/services            Services CRUD
/admin/insights            Posts CRUD (categories, tags, workflow states)
/admin/categories          Category hubs: product/post/service/portfolio categories
/admin/leads               Lead inbox + status pipeline
/admin/media               Media library
/admin/seo                 SEO records, redirects, sitemap control
/admin/settings            Site settings, locale defaults, integrations
/admin/users               Users, roles, permissions
/admin/activity            Activity log viewer
```

**Phase 0 does NOT build the dashboard.** Dashboard data requirements only (§4).

## 3. Dashboard responsibilities & data requirements

| Metric | Source (all queryable in v1 schema) | Notes |
|---|---|---|
| Total products | `products` count | |
| Published products | `products where status=published` | |
| Products with live demo | `products where demo_status=live` | demo health metric |
| Portfolio projects | `portfolio_projects` count | |
| Portfolio published | `portfolio_projects where status=published` | |
| Lab experiments | `lab_experiments` count | |
| Incoming leads (total) | `leads` count | |
| New leads (unactioned) | `leads where status=new` | primary attention metric |
| Conversion pipeline | `lead_status_history` grouped by status | funnel |
| Content status | per-domain `draft/review/published` counts | content health |
| Recent activity | `activity_logs` latest 10 | |

Dashboard is a read-only projection backed by the same queries an admin would use;
**no new dashboards-specific tables in v1** (avoid premature aggregation tables).
Widgets are components; refresh via Livewire polling (30s) in Phase 2.

## 4. Content workflow (published states)

| Content type | States |
|---|---|
| Products | `draft → published → archived` |
| Portfolio | `draft → published → archived` (client permission required for publish) |
| Lab | `draft → visible → archived` |
| Services | `draft → published → archived` |
| Posts (Insights) | `draft → review → published → archived` |

Workflow engine: enums + policy rules (who can transition). Audit entries written on
every transition (`activity_logs.action=status_change`, properties contain before/after).
No approval-chain engine in v1 (single-operator); the `review` state exists so a
future Editor role can submit content to the owner.

## 5. Admin form conventions

- **Validation:** FormRequests (per domain) with `Rules` for slugs, URLs, JSON
  localized fields, media, status transitions.
- **Livewire forms:** one Livewire component per editor (`Admin\Products\Edit`),
  saving via the same Action classes used by tests/console — no logic in Livewire.
- **Media picker:** reusable `MediaManager` component (collection-aware).
- **SEO tab:** per-locale title/description, canonical, noindex, OG fields,
  schema override (raw JSON with validation) — see [`seo.md`](seo.md).
- **Persian support:** admin UI locale switch (`en`/`fa`) with full RTL layout;
  localized fields rendered as tabbed inputs per locale (not concatenated strings).
- **Mass actions:** publish/unpublish/archive (audited); delete is soft-delete +
  restore within 30 days.

## 6. Roles & permissions (Phase 0 model)

| Role | Scope | Permissions |
|---|---|---|
| `owner` (super-admin) | everything | all permissions |
| `admin` | operations | most content + leads + media + settings (not users/roles) |
| `editor` (future) | content only | products/portfolio/lab/services/posts + media (read/write), no settings/leads/users |
| `staff` (future) | scoped | configurable subset |

Phase 0 ships `owner` only (seeded). `admin`/`editor`/`staff` roles are defined in
seed data for future activation — no custom role/UI engineering in Phase 0 (rule 40:
don't overengineer early).

## 7. Audit trail

Every admin mutation writes `activity_logs` — who, when, what, from/to. Sensitive
fields (password, tokens) are never logged. Login/logout events are logged by
middleware. Lead status changes are additionally recorded in
`lead_status_history` (business audit, survives user deletion).

## 8. Settings

`settings` table (key/value with type + `is_public` flag). Blue Control settings
sections: Site identity (name, tagline, contact email), SEO defaults (site-wide
title/description/OG image), Analytics (tracking IDs, consent config), Integrations
(email notification target, demo allowlist host), Locale (default locale, enabled
locales), Media (collection configs — advanced, developer-only).

## 9. Security posture (summary — details in [`security.md`](security.md))

- `auth:admin` guard (separate session table/guard from any future public auth).
- Rate-limited login (`throttle:5,1` + local log lockout).
- 2FA column-ready (feature in Phase 3).
- All forms CSRF-protected; Livewire built-in.
- All admin views check permissions at both route and action level (defense in depth).
- Admin links unindexable: `X-Robots-Tag: noindex`, no sitemap entries, `Cache-Control: no-store`.

## 10. Maintenance cost controls

- Reuse public domain Actions for admin writes (single code path per use-case).
- Components/design tokens shared with public UI where possible (admin uses same
  tokens, lighter layout).
- No custom admin CSS framework — same SCSS token system, admin-only component layer.
