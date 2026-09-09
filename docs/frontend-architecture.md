# Blue Studio OS — Frontend Architecture (Phase 1B)

Server-rendered Blade + token-driven SCSS + a few-hundred-byte ES modules.
**No** React/Next, no jQuery, no animation/3D frameworks (GSAP/Three.js are
reserved for later phases and are not installed).

## Layer map

```
HTTP request
   │
   ├─ Controller (presentation assembly only — no business logic)
   │     HomeController::__invoke → MetaResolver + localized config data
   │
   ├─ Blade views (pure presentation, no data access)
   │     layouts/public/app       shell: <head>/meta, skip link, header, main, footer
   │     home                     page: composes sections
   │     components/{ui,public,products,services,portfolio}   reusable units
   │
   ├─ SCSS (tokens → base → components → layouts → utilities → pages)
   │     resources/scss/app.scss            public entry
   │     resources/scss/admin.scss          admin entry
   │
   └─ ES modules (progressive enhancement only)
         resources/js/app.js                public entry (~2.2 kB / 0.9 kB gz)
         resources/js/admin.js              admin entry
         modules/core.js                    direction sync (body data attrs)
         modules/locale-switcher.js         footer/locale forms
         modules/motion.js                  IntersectionObserver reveals
         modules/navigation.js              accessible mobile drawer
         modules/accessibility.js           dialog/dropdown behavior
```

## Rules

1. **No domain logic in views.** Views receive strings/arrays/collections
   already shaped by controllers or domain services. `home.blade.php` never
   calls `config()`/`auth()`/queries; locale-dependent config mapping lives in
   the controller.
2. **Presentation contracts only.** Product/portfolio/service components
   declare their data shape in `@props` comments. No model type-hints, no
   repositories, no CRUD (later phases swap in real domain data without
   touching the components).
3. **Components are the only way to render UI.** Pages compose
   `x-ui.*` / `x-products.*` / etc.; they don't invent their own markup
   classes for controls.
4. **No fake content.** Empty data → `x-ui.empty-state`. Services are real,
   from `config('blue.services.preview')`. Header/footer anchors point at
   sections that exist on the homepage; no dead links, no invented social
   profiles.
5. **RTL native.** SCSS uses logical properties; `[dir='rtl']` overrides are
   documented and localized (select arrow background, drawer slide, dropdown
   origin, motion direction). `App\Support\Locale` is the single source of
   `lang`/`dir`; nothing in views decides direction itself.
6. **Progressive enhancement.** The site works without JS (drawer can't open
   without it — documented limitation; all content is reachable via desktop
   nav and footer). `motion.js` degrades to visible content under
   reduced-motion and when IO is unavailable.
7. **No unnecessary dependencies.** Runtime JS dependencies: zero. Build
   deps: vite, sass, laravel-vite-plugin (+ test-only playwright/axe).
   Self-hosted fonts only (Inter variable + Vazirmatn 400/700, ~91 kB total).

## Build & performance

- Vite inputs: `resources/scss/app.scss`, `resources/js/app.js`,
  `resources/scss/admin.scss`, `resources/js/admin.js`.
- CSS: browser-targeted, no framework; compiled app stylesheet ≈ 26.9 kB raw
  / **5.75 kB gzip**; fonts 91 kB (swap).
- JS: app entry 2.24 kB / **0.94 kB gzip** + shared accessibility chunk
  1.95 kB / 0.73 kB gzip.
- Fonts load with `font-display: swap`; no FOUT-blocking.
- Content is inert-first: reveals add opacity/transform only.

## Accessibility responsibilities

- Semantic landmarks: header, nav ×2, main (`id="main"`), footer, sections
  labelled via `aria-labelledby`.
- Skip link first in DOM; focus-visible rings on all controls; drawer
  focus trap + Esc + focus restore; dialogs/dropdowns handle their own
  keyboard contract.
- Axe is run as part of the E2E suite (see `tests/e2e/homepage.spec.js`) on
  CI once browser binaries are available.

## Testing

- Feature tests (Pest) cover: homepage render, SEO head, services catalog,
  empty states, 404, locale switch, RTL document attributes.
- E2E (Playwright + axe, Chromium desktop/mobile): homepage load, canonical/OG,
  LTR default, Persian switch → RTL, section anchors + empty states, drawer
  open/close, drawer focus management, axe smoke.
- CSS audits (scripted, reproducible): zero physical left/right properties,
  no pure-black backgrounds, no raw hex outside `tokens/_colors.scss`,
  token contrast matrix ≥ AA.
- Browser execution is currently deferred (sandbox cannot download Playwright
  binaries); see `docs/phase-1b-report.md` Risks.
