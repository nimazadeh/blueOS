# ADR-002 — Frontend Rendering Strategy

- **Status:** Accepted
- **Date:** 2026-09-09
- **Supersedes:** none

## Context

The public site must be fast, crawlable, and accessible; the admin must be
interactive without a separate frontend codebase; and the brief explicitly
recommends server-rendered Blade over introducing React/Next.js "because they are
popular".

## Decision

- **Public site: Blade SSR + reusable Blade components + SCSS + ES modules.**
  Zero client-side content fetching. Alpine.js for small progressive islands
  (nav, dialogs, form enhancement). GSAP/ScrollTrigger/Lenis/Three.js only inside
  the dedicated motion/3D module layers.
- **Blue Control: Laravel Livewire** (server-driven interactivity in the same
  Laravel app, mounted under `/admin`).
- **Build:** Vite + laravel-vite-plugin (ES modules, dynamic import for motion/3D).

## Alternatives considered

1. **React/Next.js frontend + API backend** — rejected: adds API contract,
  JavaScript runtime cost, SEO/CSR complexity, and a second codebase; no business
  need.
2. **Inertia.js** — rejected: turns the public site into an SPA experience,
  contradicting SSR-first SEO/perf goals; considered viable only for admin.
3. **Alpine alone (no Livewire for admin)** — rejected: admin CRUD complexity
  would sprawl into custom JS endpoints.
4. **Vue/Nuxt** — same reasons as Next.js; rejected.
5. **filament/novacustom admin** — rejected per ADR-010 preference for full control
  and shared design tokens.

## Consequences

- ✅ SEO/server rendering is native; TTFB controllable; site works without JS.
- ✅ One language stack for the whole app (PHP/Blade/Alpine/Livewire).
- ⚠️ Complexity of admin interactivity lives in Livewire components (must stay
  thin — actions remain in domain layer).
- ⚠️ Blade + SCSS requires naming discipline; design-token system is mandatory.
