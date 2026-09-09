# ADR-012 — Lead Management

- **Status:** Accepted
- **Date:** 2026-09-09
- **Supersedes:** none

## Context

Lead generation is a core business capability (Funnel A) and the main measurable
conversion. It must be a first-class domain with lifecycle, source attribution,
notes, audit history, anti-spam, and privacy.

## Decision

- **First-class `leads` domain** with fields per
  [`../lead-management.md`](../lead-management.md), status enum
  (`new → contacted → qualified → proposal → negotiation → won / lost / archived`),
  `lead_notes`, `lead_status_history`.
- **Transitions validated in `LeadStatusService`** — single mapping table for the
  UI; history row written on every change (audit + funnel reporting).
- **Source attribution:** `source` + `source_url` + `referrer` + locale set by the
  entry point; product demo-request leads are attributed as `product-demo`.
- **Submission security:** honeypot + time trap + rate limits (5/min/IP,
  3/hr/email), dedupe per email+source within 30 min, PII-less event payloads,
  HMAC-hashed IP, consent timestamp.
- **No email provider dependency in v1** — notifications log to log channel;
  provider integration is Phase 2 via `LeadSubmitted` event (no schema change).
- Leads are not soft-deleted by default; `archived` terminal; true delete is
  owner-only + audit; export (CSV) Phase 2.

## Alternatives considered

1. **Generic contact submissions table** — rejected: loses lifecycle, notes,
   source attribution, funnel reporting.
2. **Third-party CRM only** — rejected: platform must own its data; CRM sync is a
   future Phase 5 webhook via existing events.
3. **Status as free text / open enum** — rejected: lifecycle integrity requires
   closed enum.
4. **SMS/Telegram immediate notifications** — deferred as Phase 2 candidate
   (locality decision), no schema impact.

## Consequences

- ✅ Measurable conversion pipeline; robust anti-spam; privacy-first storage.
- ✅ Future CRM/automation hooks via events without rework.
- ⚠️ Status transitions must stay strictly tested (unit + feature).
- ⚠️ Notification is log-only until Phase 2 — operator must check Blue Control
  (documented release note for v1).
