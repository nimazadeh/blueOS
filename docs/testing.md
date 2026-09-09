# Blue Studio OS — Testing Strategy

**Status:** Accepted (Phase 0). Three layers: Unit → Feature → E2E. CI gates every
layer from Phase 1 onward.

---

## 1. Test pyramid & scope

```
        🔺 Browser/E2E (few, critical flows — Playwright)
      🔺 Feature (routes, forms, authz, DB behavior — Pest/PHPUnit)
    🔺 Unit (domain logic — Actions, Services, value objects, enums)
  🔺 Static (phpstan, style, eslint, blade component tests as applicable)
```

**Rules:**

- Business logic lives in Actions/Services → **unit-testable without HTTP**.
- Controllers stay thin → feature tests cover routes/validation/redirects.
- Views: a small set of Blade component tests (render assertions) + Playwright
  visual/E2E for true integration.
- Admin operations tested at feature level (Livewire tests) + E2E smoke.

## 2. Unit tests (Pest or PHPUnit — decide Phase 1; Pest recommended)

| Target | Examples |
|---|---|
| SlugService | kebab, collision suffix, unicode/Persian transliteration, reserved paths |
| LeadStatusService | legal/illegal transitions, history recording |
| Status/enums | mapping, sorting, labels, display helpers |
| MetaResolver | fallback chain, locale resolution, canonical rules, noindex rules |
| MediaService | validation decisions, variant naming, replace semantics (filesystem fake) |
| DemoLink resolver | type/status/allowlist/url fallback |
| Formatters | localize numbers, RTL isolation, price display |

## 3. Feature tests

| Area | Coverage |
|---|---|
| Public routes | 200 for every published entity; 404 for drafts/unknowns; 301 redirects |
| **Forms** | lead validation matrix (required, length, URL, email normalization, honeypot, rate limit), contact, demo request, consent |
| **Auth** | admin login success/failure/lockout, logout, session, CSRF |
| **Authorization** | permission matrix (owner vs editor/staff) on every admin route |
| Content lifecycle | create → draft → publish → sitemap/SEO updates; archive; restore; slug change → redirect |
| Media | upload validation, variant generation (fake disk), replace, delete, orphan cleanup |
| SEO | metadata rendering per page; sitemap content; robots; hreflang |
| Commerce/demo | purchase URL presence, demo link states, CSP-relevant URL allowlist |

Feature tests use `RefreshDatabase` + `Storage::fake()`; no external services.

## 4. E2E tests (Playwright + Chromium)

Run on provisioned dev/CI (see [`environment.md`](environment.md) — browsers cannot
install in this sandbox today). Critical journeys:

1. Homepage loads (SSR content visible, no JS errors)
2. Primary nav to all sections
3. Product index → detail → demo CTA → external/intent
4. Start a Project: valid submit → success; invalid → inline errors; honeybot blocked
5. Portfolio → case study → services → start project
6. Admin: login → dashboard → create/edit product → publish → visible publicly
7. Admin lead flow: lead arrives → status transitions → note → archive
8. RTL (fa): home + product + lead form — geometry/direction checks
9. Accessibility smoke: axe scan on key templates
10. Reduced-motion: `emulateMedia({ reducedMotion: 'reduce' })` — no animation triggers,
    content equivalent
11. Mobile viewport (390×844): nav drawer, forms, no horizontal overflow

## 5. Test data strategy

- Factories per domain (products, portfolio, lab, services, posts, leads, media,
  users with roles).
- **No fake clients/companies in factory data that could leak to production** —
  factories clearly namespaced `Test`; seeders for dev use obviously fictional
  names (`Acme Sample Co`).
- `DatabaseSeeder` for production creates **no** business content; only roles +
  settings defaults.
- Tests never share state; each test isolated.

## 6. CI pipeline (Phase 2 — executed on GitHub Actions)

| Step | Tool | Gate |
|---|---|---|
| syntax/lint | Pint, phpstan (level 5–6), eslint | fail on errors |
| unit+feature | Pest (mysql service), coverage ≥70% domain logic | fail on fail |
| build | npm ci + vite build | fail on fail |
| audit | composer audit, npm audit | fail on critical |
| E2E | Playwright (Chromium, 3 projects: desktop/mobile/rtl) | fail on fail |
| a11y | axe scan (part of E2E) | fail on serious/critical |
| perf | Lighthouse CI budgets | fail on budget breach |

## 7. Test conventions

- One behavior per test; descriptive names (`it('publishes product and invalidates sitemap')`).
- No sleep/hardcoded waits; Playwright auto-wait; tests deterministic (freeze time
  where needed).
- Test seeders use `Setting` defaults; no reliance on `.env` secrets.
- CI runs MySQL 8 (matching production), not SQLite — schema differences must not
  hide.
- Flaky-test quarantine: failures rerun once, then investigate; never disable a test
  silently.

## 8. Coverage targets (Phase 1 baseline)

- Domain Actions/Services: ≥ 80% line coverage (critical paths 100%)
- Public feature routes: full happy-path + validation matrix
- Admin feature authz: full permission matrix
- E2E: 10 critical journeys (above), smoke on release

## 9. Out of scope in Phase 0/testing plan

- Load/soak testing (Phase 5 candidate — resource decisions, not architecture).
- Visual regression suites (candidate Phase 3 — Playwright snapshot tooling).
- Property-based/fuzz (Phase 3+; security fuzz on upload parsing considered).
- Cross-browser matrix beyond Chromium/Firefox/WebKit where Playwright supports
  and CI budget allows (documented as Phase 3 stretch).
