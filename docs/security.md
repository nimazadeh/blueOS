# Blue Studio OS — Security Foundation

**Status:** Accepted (Phase 0). This is the baseline security architecture; each
area is enforced in Phase 1 and verified in QA.

---

## 1. Core posture

- Laravel conventions first; every framework safety default stays enabled.
- **Defense in depth:** validation at the boundary + authorization at the action +
  escaping at the view + CSP at the server.
- **Least privilege:** every admin action checks permission; public forms are scoped,
  rate-limited, and validated.
- **No secrets in source:** `.env` is gitignored; `.env.example` contains keys only,
  no values; deployment injects secrets via environment/secret manager (see
  [`deployment.md`](deployment.md)).
- **No invented security layers:** no custom encryption wrappers, no hand-rolled
  auth, no magic IP allowlists.

## 2. Authentication & session

| Area | Design |
|---|---|
| Guards | `web` (public — no public accounts in v1), `admin` (Blue Control) |
| Login | email + password (Argon2id via `PASSWORD_ARGON2ID` config) |
| Rate limiting | `throttle:5,1` on login; DB-backed failed-login audit with lockout for repeated failures (middleware) |
| Sessions | cookie-based, `SameSite=Lax`, `HttpOnly`, `Secure` in prod, short idle timeout config; distinct session files per guard |
| Password reset | token flow (enabled in Phase 3 alongside 2FA; reset emails via log in v1 dev) |
| 2FA | schema-ready (`two_factor_enabled`), implementation Phase 3 |
| Remember me | disabled by default (config) |

## 3. Authorization (RBAC)

- Roles/permissions: `spatie/laravel-permission` ([`adr/0006-authentication-strategy.md`](adr/0006-authentication-strategy.md)).
- **Every** admin route checks middleware; **every** write action checks a policy or
  permission (never trust route middleware alone).
- Public write paths (leads) use FormRequest validation; no authenticated user model
  is required.
- Future API: same permission checks at resource layer, no bypass.

## 4. Input validation & injection protection

- FormRequests for every public/admin write (typed rules, per-locale fields).
- **SQL injection:** Eloquent query builder (parameter binding) only; raw SQL
  requires review + bindings. No string-interpolated where clauses.
- **XSS:** Blade auto-escaping on; never `{!! !!}` for user input; rich-text
  sanitized server-side (HTMLPurifier or equivalent) before storage; CSP as second
  layer.
- **CSRF:** default Laravel `VerifyCsrfToken` for web guard; Livewire token built-in;
  all public forms use `@csrf`.
- **Mass assignment:** `$fillable`/`$guarded` explicit on every model; hidden
  `$hidden` for sensitive fields.
- **Uploads:** MIME sniff + extension whitelist + size/dimension caps; SVG disabled
  by default (see [`media-architecture.md`](media-architecture.md#4-validation));
  files stored outside webroot; public disk only for intended variants; no execution
  of uploaded files (PHP/HTML) — disallowed extensions served as `application/octet-stream` if needed.

## 5. Rate limiting & abuse

| Endpoint | Limit |
|---|---|
| Login (admin) | 5/min/IP |
| Lead submit | 5/min/IP, 3/hr/email |
| Contact | 3/min/IP |
| Demo request | 3/min/IP |
| General public routes | no aggressive limit (static SSR) |

- Honeypot + time-trap on lead form; IP hashed (HMAC) — raw IP never stored/logged
  in business data.
- 429 responses are branded, locale-aware, and link to contact (no raw errors).

## 6. Secure headers & CSP (production nginx)

| Header | Value |
|---|---|
| `Strict-Transport-Security` | `max-age=31536000; includeSubDomains` |
| `Content-Security-Policy` | `default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline' (tokens only if unavoidable); img-src 'self' data: blob: https://{cdn}; frame-src 'self' {demo-allowlist}; connect-src 'self'; font-src 'self'` |
| `X-Content-Type-Options` | `nosniff` |
| `X-Frame-Options` | `SAMEORIGIN` (except `/admin` never framed; demo links are external) |
| `Referrer-Policy` | `strict-origin-when-cross-origin` |
| `Permissions-Policy` | `geolocation=(), camera=(), microphone=(), payment=()` |
| `X-Robots-Tag` | `noindex` on `/admin`, 404/500/error pages |
| Cache | `Cache-Control: no-store` on admin + auth routes; public pages short cache with ETag |

- CSP `frame-src` allowlist is configured via `settings.integrations.demo_allowlist`
  (defense: embedded demos only from configured origins).

## 7. Secrets & environment

- `.env` per environment (gitignored); `.env.example` committed with placeholders.
- Production secrets injected by deployment (env vars / platform secret store),
  never in images/commits.
- `APP_KEY` required; regenerate on environment clone. `.key` staging files gitignored.
- No third-party credentials in code or configs; all via `env()`.

## 8. Logging & observability

- **Authentication events:** login success/failure, logout, password reset, 2FA —
  logged with actor id (if any), IP hash, timestamp, guard.
- **Admin activity:** every create/update/delete/status change through
  `activity_logs` (actor, action, subject, before/after **redacted**).
- **Errors:** Laravel exception log channel; production single JSON line format
  (structured, no PII; URI paths only); 500s show generic branded page.
- **Business events:** leads created/status-changed at `info`.
- **Rate-limit hits:** `warning`, no PII.
- Storage: `storage/logs` daily locally; production log drain (stdout in container)
  so host logging can collect it. No custom observability platform in v1; external
  error tracker is a documented Phase 5 option.

## 9. Admin security specifics

- `/admin` route group: `auth:admin`, `verified`, `throttle`, permission middleware,
  activity-log middleware, no-store caching, noindex.
- Separate admin login page; no credentials in code; no "demo" admin accounts in
  seeded data (seed only documentation of how to create owner).
- Failed login lockout + audit. Session fixation protection (Laravel default
  `regenerate`). Admin forms CSRF-protected.
- Admin views never render raw user input; lead notes escaped; attachments never
  served from admin with user-controlled content type.

## 10. Dependencies & vulnerabilities

- Composer `composer audit` and npm `npm audit` in CI gate (Phase 2+).
- Lockfiles committed; avoids floating versions.
- Dependency review: never add a package without documented reason (see
  [`architecture.md`](architecture.md#8-dependency-policy)).
- SRI for any external script (there should be **none** in v1 — all assets self-hosted).

## 11. Security QA matrix (see [`qa.md`](qa.md))

- Auth flows (login, logout, lockout, 2FA later)
- Permissions (role matrix tests)
- XSS (lead fields, blog body, media alt, admin fields)
- SQLi attempts on slugs/query params
- Upload abuse (fake MIME, oversized, SVG, traversal names)
- Rate limits / honeypot
- Headers/CSP
- CSRF on all forms; cookie attributes

## 12. Deferred decisions

- 2FA mechanism (TOTP vs WebAuthn) — Phase 3 ADR.
- External error tracker — Phase 5.
- Session store (Redis vs file) — deployment decision, not schema.
- WebAuthn/passkeys — Phase 3+ if required.
