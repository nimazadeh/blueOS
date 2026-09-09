# ADR-006 — Authentication & Authorization Strategy

- **Status:** Accepted
- **Date:** 2026-09-09
- **Supersedes:** none

## Context

Blue Control is an internal system; the platform will later need editor/staff roles,
future client portal, and possibly a public account surface. Phase 0 must define a
strategy that is secure now and extensible later without over-engineering RBAC.

## Decision

- **Authentication:** Laravel session auth, separate `admin` guard + session config;
  Argon2id hashing; rate-limited login (5/min/IP) + failed-login lockout logging;
  CSRF default; HttpOnly/Secure/SameSite cookies; `two_factor_enabled` column
  (2FA implementation deferred to Phase 3, TOTP/WebAuthn decision then).
- **Authorization:** `spatie/laravel-permission` (roles + permissions + cached
  middleware); route middleware **and** in-action permission checks (defense in
  depth).
- **Roles:** seed `owner` now; define `admin`, `editor`, `staff` in seed data for
  future activation. No custom role-management UI in Phase 0.
- **Admin session controls:** idle timeout config, session regeneration, no remember
  token by default, audit log on login/logout/action.
- **Future:** public accounts (client portal) use the **web guard** (separate from
  admin), so portal users can never assume admin rights; client portal tables in
  Phase 7.

## Alternatives considered

1. **Hand-rolled roles/permissions** — rejected: security-sensitive code, easily
   wrong; spatie is maintained, tested, and Laravel-idiomatic.
2. **Laravel Jetstream/Breeze with Teams** — considered for admin scaffolding;
   rejected for full control over the admin UX and token system (no public account
   need in v1). Breeze may be a starting scaffold for `admin` auth (decision at
   Phase 1 scaffold).
3. **Sanctum API tokens as primary auth** — rejected: admin is a web UI; sessions
   are the correct model; Sanctum may appear with the Phase 5 API.
4. **Single superuser only (no roles)** — rejected for future editor/staff need;
   schema cost of roles now is low (spatie standard tables).

## Consequences

- ✅ Proven, testable auth; clean future extension to editor/staff/portal.
- ✅ Admin protected at multiple layers; auditability built in.
- ⚠️ spatie permission middleware caching requires cache config in prod (documented).
- ⚠️ 2FA implementation deferred — accept residual risk until Phase 3 (single-operator
  internal admin mitigates).
