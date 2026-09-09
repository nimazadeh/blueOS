# Blue Studio OS — Performance Strategy

**Status:** Accepted (Phase 0). Performance targets are architectural constraints,
measured from the first page built.

---

## 1. Targets (initial budget, verified in Phase 1 with real build)

| Metric | Desktop | Mobile (mid-range 4G) |
|---|---|---|
| LCP | ≤ 2.0s (target ≤ 1.8s) | ≤ 2.5s |
| INP | ≤ 200ms | ≤ 200ms |
| CLS | ≤ 0.05 | ≤ 0.10 |
| TTFB | ≤ 200ms (cache hit) | ≤ 400ms |
| Total JS (public initial) | ≤ 120 kB gz | ≤ 120 kB gz |
| Motion JS initial | ≤ 40 kB gz (excluding three) | same |
| Three.js chunk | 0 until used; ≤ 180 kB gz when loaded | same |
| Images above fold | ≤ 250 kB (srcset) | ≤ 150 kB |
| Image bytes total per page | ≤ 1.2 MB | ≤ 900 kB |
| Fonts (CSS + woff2) | ≤ 150 kB (subsets, woff2) | ≤ 150 kB |
| Requests (critical path) | ≤ 25 | ≤ 30 |

Budget enforcement: Lighthouse CI thresholds in Phase 2 QA; `performance` doc is the
gate; any new heavy feature must document its cost and include a budget.

## 2. Rendering & server

- **SSR Blade** — no client-side content fetch; zero request waterfall for content.
- Blade fragment caching on index pages; `config:cache`/`route:cache` in production.
- Eager loading of relations on list pages (no N+1); DB indexes per
  [`database.md`](database.md).
- opcache + JIT (php.ini: `opcache.enable=1`, JIT on in prod), HTTP/2+ (server config).
- Compression: brotli/gzip at edge; static assets fingerprint + immutable cache.
- CDN/object storage for media; same origin or self-hosted CDN; no third-party fonts
  CDN (self-hosted via npm).

## 3. Asset pipeline

- Vite multi-entry: `app` (core CSS/JS), `motion` (lazy via dynamic import),
  `three/` (dynamic per module), `admin` (Blue Control, loaded only on `/admin`).
- SCSS output minified, source maps off in prod; CSS split by entry.
- ES modules, tree shaking; no `lodash`-scale dependencies. Every dependency
  justified ([`architecture.md`](architecture.md#8-dependency-policy)).
- Fonts: `font-display: swap` for body; critical display font preloaded; unused
  weights not loaded; Persian subset only when `fa` (lazy-loaded per locale).
- Images: generated variants + `srcset`; `loading=lazy` below fold;
  `fetchpriority=high` for LCP; explicit dimensions (`aspect-ratio`) → CLS 0.

## 4. Animation / 3D performance

- Motion init is deferred (idle) and uses transform/opacity only; ScrollTrigger
  single scroller; pause off-screen; destroy on leave (see
  [`animation-system.md`](animation-system.md#8-performance-budgets)).
- 3D zero-cost until used; capability-tiered quality; fixed aspect container (no CLS);
  dispose on destroy; failure → static fallback (see
  [`threejs-architecture.md`](threejs-architecture.md)).

## 5. Web Vitals measurement plan

- **Lighthouse CI** in Phase 2 (desktop + mobile profiles) on key templates
  (home, product, portfolio, services, insights, start-project, admin login).
- **Real-user monitoring:** opt-in Core Web Vitals beacon (see
  [`analytics.md`](analytics.md)) — Phase 5 candidate; zero third-party in v1.
- **Manual:** Chrome DevTools performance traces on mobile emulation before release;
  long-task < 50ms; no layout thrash in scroll handlers.

## 6. Progressive enhancement rules

- Site **must be usable without JS** for content pages (SSR provides all content;
  JS only enhances motion/demo/forms).
- WebGL/3D missing → static fallback (already specified).
- Forms work without JS (server-side validation + redirect); JS only adds live
  validation/honeypot enhancements.
- Local storage/cookies optional; nothing blocks content when disabled.

## 7. Media/font cost rules

- No uncompressed PNG for photos (WebP/AVIF variants); SVG only for icons/logo.
- No hero images > 200KB (responsive variant selected by `sizes`).
- Lazy-load `iframe` demos (demo panel loads on interaction/scroll; `loading=lazy`
  on iframe; no autoplay).
- Font subsetting: Latin subsets + Persian subset loaded per locale; `font-synthesis`
  off to avoid fake bold.

## 8. Monitoring & regressions

- CI runs Lighthouse budget checks (Phase 2).
- Production: Laravel logs + optional external monitor (Phase 5); uptime via
  deployment provider.
- Every PR touches performance doc + budget if it adds heavy assets; performance
  is a review checklist item (see [`qa.md`](qa.md)).

## 9. Deferred

- AVIF generation (Phase 2 — validate encoder toolchain in sandbox/provisioned env).
- HTTP/3 + edge optimization (deployment choice).
- Core Web Vitals RUM (Phase 5).
- Full-text search cost model (Phase 4).
