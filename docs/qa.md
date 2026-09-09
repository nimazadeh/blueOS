# Blue Studio OS — QA Strategy

**Status:** Accepted (Phase 0). This is the future QA matrix and release gate for
all phases.

---

## 1. QA dimensions & gates

| Dimension | What it verifies | Method | Gate |
|---|---|---|---|
| Functional | every feature works end-to-end | Feature + E2E | ✅ required |
| Visual | UI matches intended design | design tokens + component review + visual diff (Phase 3) | ✅ required (manual + partial auto) |
| Responsive | desktop 1440+, tablet 768–1024, mobile 390 | Playwright viewports + manual | ✅ required |
| RTL | geometry, direction, typography, mixed content | dual-locale E2E + manual Persian pass | ✅ required (first-class) |
| Accessibility | WCAG 2.2 AA | axe + keyboard/screen-reader manual | ✅ required |
| Performance | CWV + budget | Lighthouse CI | ✅ required |
| Security | auth, forms, uploads, headers | feature tests + manual review | ✅ required |
| Browser | Chromium, Firefox, WebKit (Playwright-supported) | CI (Phase 3 stretch: full matrix) | ⚠️ Chromium required; others follow |
| SEO | metadata, sitemap, redirects, noindex | feature tests + crawl smoke | ✅ required |

## 2. Functional QA matrix (future release checklist)

- [ ] Home page renders from SSR, no JS error
- [ ] Products: index → detail → screenshots → demo → purchase (link behavior)
- [ ] Demo states: live/pending/unavailable all render correct CTA
- [ ] Portfolio: case study renders all sections; related content links
- [ ] Lab: experiment states render; no product-style CTAs
- [ ] Services: index/detail; outcomes language
- [ ] Insights: list/filter/detail/related; draft invisible publicly
- [ ] Start a Project: validation, success, error, honeypot, rate limit
- [ ] Contact: works, source tracked
- [ ] Locale switch en/fa: all pages, hreflang, RTL, mixed content
- [ ] Admin: login, dashboard metrics, product/portfolio/lab/service/post CRUD,
      media upload, lead pipeline, settings, users/roles, SEO/redirects
- [ ] 404/403/500 branded pages
- [ ] Sitemap/robots correctness after publish/unpublish/archive

## 3. Visual QA principles

- Components are built from tokens; a **visual QA pass compares against the token
  spec**, not against "looks nice".
- Golden/token regression: automated contrast + spacing checks in CI (Phase 2).
- Screenshot pairs (LTR/RTL) for every new component in Phase 3.
- No animation-only states: screenshots include static equivalents.

## 4. Responsive & RTL QA

- Breakpoints: mobile-first, 390 / 768 / 1024 / 1440 / 1920.
- Check: horizontal overflow, tap targets, sticky nav, drawer, tables, forms,
  galleries, demos (iframe behavior), footer, focus order in both dirs.
- Persian: font rendering (Vazirmatn), numerals where appropriate, mixed English
  product names, code blocks, URLs/emails remain LTR, punctuation positioning.
- Fonts: no fallback to wrong script (Persian text must not render with Latin font).

## 5. Accessibility QA (manual + automated)

- axe-core scan (E2E) on all public templates + admin screens; zero serious/critical.
- Keyboard-only walkthrough (documented §2 in
  [`accessibility.md`](accessibility.md)).
- Screen reader pass: NVDA (Windows) + VoiceOver (macOS) on key journeys — release
  gate manual check (document owner responsibility; tooling in CI covers basics).
- Reduced-motion emulation: no motion elements active; content present.
- Contrast: token-level check + snapshot.

## 6. Performance QA

- Lighthouse CI budgets (mobile/desktop) on 10 key URLs.
- Manual: DevTools performance trace (mobile throttle) on home + product detail;
  no long tasks > 50ms during scroll; no layout shift on image load.
- Network: image weight per page; font payload; JS chunk analysis (vite
  `--report`-style).
- 3D: only when modules exist; verify fallback timing (< 300ms) and FPS on low tier.

## 7. Security QA

- Public: forms (XSS injection attempts via description/name/links), upload abuse
  (fake MIME, SVG, oversized, traversal), rate-limiting behavior, CSP headers.
- Admin: permission bypass attempts (role escalations), CSRF, session cookies,
  login lockout, audit logs verify.
- Secrets: scan repo for `.env` / keys / tokens (CI secret-scan step).
- Dependency audit (composer/npm).

## 8. Browser matrix (Phase 0 decision)

| Browser | Phase 1 | Phase 3 target |
|---|---|---|
| Chromium (latest 2 versions) | ✅ required | ✅ |
| Firefox | manual | ✅ automated where Playwright supports |
| WebKit/Safari | manual responsive checks | ✅ automated (best-effort; Apple WebKit quirks documented) |
| Mobile Safari/Chrome | manual device check | ✅ automated (Playwright devices) |

## 9. Defect severity & release gates

| Severity | Definition | Release gate |
|---|---|---|
| S1 Critical | data loss, security breach, broken conversion | BLOCK release |
| S2 Major | core flow broken with workaround | BLOCK release |
| S3 Minor | polish/edge case, no blocker | allowed with documented known-issue |
| S4 Cosmetic | visual nit | triage backlog |

Known-issues list lives in `docs/qa-known-issues.md` (created when first issue
arises) and must be reviewed each release — never hidden.

## 10. QA cadence

- Every PR: automated CI (test + build + lint + a11y/axe + Lighthouse on affected).
- Every release (staging → prod): full manual QA matrix (§2) + RTL + a11y + security
  spot checks + browser matrix.
- Monthly: dependency audits, broken-link crawl, sitemap validation, CWV review.

## 11. Phase 0 QA status

- No code exists → nothing to functionally QA. Phase 0 gate is **documentation
  consistency**, verified by: cross-references resolve; schema tables match
  [`database.md`](database.md); ADRs cover every major decision; acceptance list
  (§42 of the brief) checked in [`phase-0-report.md`](phase-0-report.md).
