# ADR-004 — Animation Architecture

- **Status:** Accepted
- **Date:** 2026-09-09
- **Supersedes:** none

## Context

Blue Studio will use advanced motion (GSAP/ScrollTrigger/Lenis/Three.js), but
animation must be a system supporting hierarchy, storytelling, and interaction —
not decoration. Current phase must not install libraries; architecture must make
motion modular, performant, and reduced-motion-safe.

## Decision

- **MotionManager registry** (`resources/js/motion/`) owns lifecycle: init per
  feature module, cleanup on destroy, capability gating.
- **Stack:** GSAP 3.15+ (with ScrollTrigger) for orchestrated scroll/entrance
  motion; **Lenis** as optional smooth-scroll layer (disabled on touch/reduced-
  motion); **Alpine.js + CSS transitions** for micro-interactions; **CSS
  transitions are the default** for simple UI states.
- **Module boundaries:** `reveal.js`, `hero.js`, `nav.js`, `dialog.js`,
  `gallery.js`, `counters.js` (real metrics only) — no giant global animation file.
- **Reduced motion:** `prefers-reduced-motion` sets `motion-level: none` (GSAP
  timelines skip, Lenis off, 3D off) with CSS baseline media query.
- **Performance:** transform/opacity only, one ScrollTrigger scroller, off-screen
  pause, destroy-all lifecycle, ≤40 kB initial motion JS.

## Alternatives considered

1. **Pure CSS + IntersectionObserver** — considered for v1 baseline (reveals use
   this first); rejected as the *final* system because complex choreography
   (hero sequences) needs timeline control and cleanup guarantees GSAP provides.
2. **Framer Motion / React-based** — incompatible with Blade SSR decision.
3. **Lottie** — not rejected permanently; "where appropriate" per brief, but no use
   case in v1 → deferred (avoid dependency without a reason).
4. **Animista/aos/etc. utility libs** — rejected: template-like, no lifecycle ownership.
5. **self-written animation framework** — rejected: reinventing GSAP; maintenance cost.

## Consequences

- ✅ Motion is testable, per-module, and safe to extend (Lab/3D later).
- ✅ Accessibility and performance are architectural properties, not patches.
- ⚠️ GSAP license/agreement applies (standard commercial license acceptable; no
  enterprise features needed) — reviewed at install time.
- ⚠️ Requires discipline: feature modules must not reach into each other's DOM.
