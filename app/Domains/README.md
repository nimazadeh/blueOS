# Domains (modular monolith)

Each sub-directory is a **bounded domain** (see `docs/architecture.md` and
ADR-001/ADR-011). Domain code owns its models, migrations, observers, actions,
requests, routes and views. Domain modules must not reach into each other's
models; cross-cutting concerns go through `App\Core\*` services.

Phase 1A creates the boundaries only. Domain implementations arrive in their
own phases:

| Domain | Purpose | Lands |
|---|---|---|
| Products | Blue Studio–owned, commercial products (+ demo model) | Public experience phase |
| Portfolio | Client / commissioned work (case studies) | Public experience phase |
| Lab | Experimental / research work | Public experience phase |
| Services | Outcome-led service offers | Public experience phase |
| Content | Insights/blog, categories, tags | Content phase |
| Leads | Project requests + lifecycle pipeline | Blue Control phase |

Naming example once a domain is implemented:

```
app/Domains/Products/
├── Actions/
├── Http/Controllers/
├── Models/
├── Observers/
└── Policies/
```

Shared kernel lives in `app/Core/` (Settings, Slug, SEO, Media, Activity).
