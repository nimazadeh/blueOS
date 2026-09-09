# Blue Studio OS — Lead Management

**Status:** Accepted (Phase 0). Leads are a **core business domain**, not a
contact-form side effect.

---

## 1. Data captured

| Field | Required | Notes |
|---|---|---|
| name | ✅ | 2–150 chars |
| email | ✅ | validated, normalized lowercase |
| company / brand | optional | 0–190 chars |
| project type | ✅ (select) | website / webapp / saas / backend / automation / bot / python / integration / ai / custom / other |
| budget range | recommended | `under-1k`, `1k-5k`, `5k-20k`, `20k-50k`, `50k+`, `not-sure` (display ranges, no math) |
| timeline | recommended | `asap`, `1-3-months`, `3-6-months`, `flexible` |
| description | ✅ | 10–4,000 chars, plain text |
| relevant links | optional | array of http(s) URLs, max 5 |
| consent | ✅ | privacy consent timestamp |
| source / source_url | system | page + route that submitted |
| referrer | system | HTTP referrer |
| locale | system | form locale |
| IP hash, user agent | system | hashed IP only (no raw IP stored) |

## 2. Lifecycle & status model

```
New ──► Contacted ──► Qualified ──► Proposal ──► Negotiation ──► Won
 │          │             │             │              │            │
 └──────────┴─────────────┴─────────────┴──────────────┴──► Lost / Archived
```

Enums (PHP backed, `varchar(20)` in DB):

| Status | Meaning | Who can set | Allowed transitions |
|---|---|---|---|
| `new` | received, unactioned | system | → contacted, archived |
| `contacted` | first outreach done | staff | → qualified, lost, archived |
| `qualified` | real opportunity confirmed | staff | → proposal, lost, archived |
| `proposal` | proposal sent | staff | → negotiation, won, lost, archived |
| `negotiation` | in discussion | staff | → won, lost, archived |
| `won` | converted to client | owner | → archived (terminal, reversible only by owner) |
| `lost` | closed without conversion | staff | → reopened via `new` (owner only), archived |
| `archived` | permanently shelved | staff | none (except owner restore) |

Transitions are **validated in `LeadStatusService`** (single source of truth), row
history written to `lead_status_history`, and UI renders dropdowns from the
transition map — no on-screen invalid states.

## 3. Capture paths (funnels map to `source`)

| Entry | Route | `source` |
|---|---|---|
| Start a Project | `/start-a-project` | `website` |
| Contact form | `/contact` | `contact` |
| Request a demo (product) | `/products/{slug}?demo=request` | `product-demo` |
| Product inquiry (fallback CTA) | `/products/{slug}` → start project | `website` (source_url carries product page) |
| Portfolio CTA | `/portfolio/{slug}` | `portfolio` |
| Future extended path | any webhook/API (Phase 5) | `api` |

## 4. Submission pipeline (public)

```
Form (HoneypotField hidden input + browser-fill bait, 0 tabs)
  → RateLimiter: 5/min/IP, 3/hour/email (429 with friendly message)
  → FormRequest validation (LeadRequest)
  → CreateLead action:
      • normalizes email, trims, strips HTML
      • stores source metadata + hashed IP
      • dedupe check: same email + source within 30 min → merge/reject silently
        (update original's source_url; no duplicate rows)
      • schedules notification (mail log in v1; provider in Phase 2)
      • fires LeadSubmitted event (analytics hook, audit)
  → 302 to /start-a-project/success (PRG — no re-submit on refresh)
```

## 5. Blue Control lead inbox

- List with filters: status, project type, budget, timeline, source, date range.
- Sortable columns; search email/name/company.
- Detail drawer/page: all fields + edit + notes + status transition UI + timeline.
- Notes: `lead_notes` (immutable, author + timestamp appended to timeline).
- Quick actions: mark contacted, qualify, move to proposal, archive, assign owner.
- Export (CSV with consent + status columns) — Phase 2.
- Lead notification setting: `settings.integrations.lead_notification_email`.

## 6. Anti-spam & privacy

- Honeypot + time-trap (`created_at` delta < 3s reject) + rate limits per IP/email.
- `ip_hash` = HMAC-SHA256 of IP with app key (never store raw IP or email-hash leaks).
- `consent_at` recorded; export/download supports per-lead consent evidence.
- Leads are never soft-deleted by default; `archived` is the normal terminal state.
  True deletion is owner-only with audit trail.
- No third-party analytics on the lead form without consent gating (see
  [`analytics.md`](analytics.md)).

## 7. Events (anticipating analytics & automation)

| Event | Payload (no PII in analytics events) |
|---|---|
| `LeadSubmitted` | lead_id, source, source_url, locale, project_type |
| `LeadStatusChanged` | lead_id, from, to, changed_by |
| `LeadNotified` | lead_id, channel, outcome |
| `LeadConverted` | lead_id, client_ref (when won) |

Events are Laravel domain events now; queued listeners in Phase 2 (notification,
dashboard counters) — no event-bus infrastructure in v1.

## 8. Open items (deferred)

- CRM sync (e.g., Notion/HubSpot webhook) — Phase 5, via existing events.
- Email provider integration (SMTP/transactional) — Phase 2.
- Proposal documents — Phase 7 (client portal).
- Bulk outreach automation — out of scope until explicitly requested.
