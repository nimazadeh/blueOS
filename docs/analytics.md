# Blue Studio OS — Analytics Event Model

**Status:** Accepted (Phase 0). v1 does **not** implement an analytics platform.
This document defines the event model so Phase 5 can add real tracking without
schema/contract changes.

---

## 1. Principles

- Catalog-driven events, single source of truth (`config/analytics.php`).
- **No PII in analytics events.** Event payloads contain IDs and category/source
  strings only. Name/email stay in the lead domain, never in analytics payloads.
- Consent-gated: analytics only activate after consent where required (Persian/EU
  context); default v1 = **no third-party analytics**, first-party events only when
  opted in.
- Server-side events (Leads, admin) are primary; client-side events (clicks, views)
  are a progressive enhancement recorded by the same event catalog.
- Events are versioned (`event_name` + `v`); unknown events are ignored, never fatal.

## 2. Event catalog

| Event | Trigger | Payload (first-party) |
|---|---|---|
| `page_view` | every public page render (server-side middleware) | path, route, locale, section, status, referrer domain |
| `product_view` | product detail render | product_id, slug, locale |
| `demo_click` | demo CTA click (client) | product_id, demo_type, demo_status, target_origin |
| `purchase_click` | purchase/marketplace CTA click | product_id, target_origin |
| `project_start` | `/start-a-project` view | source, source_url, locale |
| `lead_submitted` | lead created (server) | lead_id, source, project_type, locale, outcome |
| `contact_click` | contact CTA click | target (contact/email/copy), section |
| `nav_click` | primary nav click | target_route, section |
| `portfolio_view` / `service_view` / `lab_view` / `insight_view` | detail renders | entity id, slug, locale |
| `admin_login` / `admin_logout` / `admin_action` | auth/activity hooks | actor_id, action, subject_type (admin domain, not marketing analytics) |
| `search_*` (Phase 4) | search use | query_hash, results_count |

## 3. Transport strategy (future)

- **v1:** server-side event log only (optional `analytics_events` append-only table
  in Phase 5, or structured JSON logs — decision deferred to Phase 5 ADR).
- **Phase 5 option A:** first-party self-hosted collector (`/api/events`, batched,
  signed) — keeps data ownership.
- **Phase 5 option B:** privacy-respecting third-party (e.g., Plausible/GA4 with
  consent) — chosen only after ownership review; site must work with zero analytics.
- No tracking pixels, no fingerprinting, no cross-site data.

## 4. Dashboard reporting (Phase 3+)

Reuses event catalog for: product view → demo click → purchase click funnel,
project funnels, page performance (via Core Web Vitals reporting endpoint, opt-in).

## 5. Consent model

| Scope | Behavior |
|---|---|
| Consent not given | zero events sent to third parties; server-side counters only |
| Consent given | first-party events + configured provider |
| Persist | `consent` cookie (locale-aware), clearable, expires 12 months |

## 6. Deferred decisions

- Exact storage (table vs structured logs) — Phase 5 ADR.
- Whether to self-host Plausible-like collector — Phase 5.
- Event retention policy — Phase 5.
