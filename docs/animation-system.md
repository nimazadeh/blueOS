# Blue Studio OS — Animation System

**Status:** Accepted (Phase 0). No animation library is installed in Phase 0; this
is the architecture that Phase 2 (and later) must implement.

---

## 1. Philosophy

**Animation is a system, not decoration.** Motion supports hierarchy, storytelling,
product discovery, interaction, and brand identity. It never sacrifices performance,
accessibility, SEO, or mobile usability.

Allowed categories:

1. **Entrance reveals** — section/heading/card reveals tied to scroll position
   (hierarchy + storytelling).
2. **Micro-interactions** — hover/active/press states, focus transitions, button
   feedback (interaction).
3. **State transitions** — dialogs, drawers, toasts, tab/category switches (UX clarity).
4. **Hero/feature motion** — restrained, purpose-built; the only place where larger
   motion is justified.
5. **3D/WebGL** — isolated, opt-in, context-specific (see
   [`threejs-architecture.md`](threejs-architecture.md)).

**Not allowed:** meaningless looping decoration, parallax-for-parallax, scroll-jacking,
autoplaying carousels, cursor-follow tracers, glow pulses, or any animation that
doesn't serve one of the above categories.

## 2. Stack decision

| Tool | Role |
|---|---|
| **GSAP 3.15+** (incl. ScrollTrigger) | timeline/scroll orchestration; declarative |
| **Lenis 1.3+** | smooth scroll (optional per layout; off on mobile/reduced-motion) |
| **Alpine.js** | toggle/state micro-interactions (CSS transitions) |
| **Three.js 0.186+** | only inside 3D module boundary (separate lazy chunk) |
| **CSS transitions** | first choice for micro-interactions; JS only when state must be choreographed |

Rationale — [`adr/0004-animation-architecture.md`](adr/0004-animation-architecture.md):
GSAP + ScrollTrigger chosen over pure CSS/IntersectionObserver for timeline control
and cleanup guarantees; Lenis chosen only as smooth-scroll layer with reduced-motion
and touch-device disabling. Every dependency is deliberate; no unneeded library.

## 3. Module structure

```
resources/js/
├── core/            — app boot, init registry, prefers-reduced-motion guard
├── motion/
│   ├── index.js     — MotionManager (registers feature modules, owns lifecycle)
│   ├── prefers-reduced-motion.js
│   ├── reveal.js    — ScrollTrigger reveals (data-reveal)
│   ├── hero.js      — hero entrance (only on home)
│   ├── nav.js       — header transitions, mobile drawer
│   ├── dialog.js    — accessible dialog/drawer show-hide
│   ├── gallery.js   — product gallery, lightbox
│   └── counters.js  — number transitions (real metrics only)
└── three/           — isolated 3D entry (see threejs-architecture.md)
```

**Rule:** `resources/js/motion/index.js` is a *registry*, never a giant animation
file. Each module owns its selectors, its cleanup, and its reduced-motion behavior.

## 4. Lifecycle & cleanup

```
DOMContentLoaded (defer)
  → MotionManager.init()
      → reads feature flags (data attributes, capability checks)
      → registers modules; each module:
          • binds to its scope (`[data-motion="reveal"]`)
          • reads options from data attributes
          • registers ScrollTriggers / listeners
          • returns cleanup() function
  → pagehide/route-change: MotionManager.destroy() → cleanup everything
  → IntersectionObserver based pause for off-screen ScrollTriggers (perf)
```

- No global event listeners left behind; every `ScrollTrigger.create` is killed on
  `destroy()`.
- Modules re-initialize safely if DOM is swapped (Livewire/alpine) — idempotent init.
- If a module throws, MotionManager catches → logs → **page remains fully functional**
  (progressive enhancement by construction).

## 5. Reduced motion & capability

- `prefers-reduced-motion: reduce` → MotionManager sets `motion-level: none`;
  all GSAP timelines skip (`.duration(0)`/immediate end), Lenis disabled, no
  auto-playing/scroll-driven video-parallax, CSS respects same media query.
- `motion-level` options: `full` (default), `reduced` (CSS-only transitions),
  `none` (static).
- Touch devices: Lenis off; ScrollTrigger still fine; no scroll-dependent transforms
  on mobile that cause jank (avoid `position: sticky` + transform conflicts).
- `@media (prefers-reduced-motion: reduce)` in CSS disables transforms/transitions
  globally as a baseline safety net.

## 6. Motion tokens

| Token | Value | Use |
|---|---|---|
| `--motion-fast` | 150ms | micro-interactions |
| `--motion-base` | 300ms | component transitions |
| `--motion-slow` | 500ms | hero/entrance |
| `--ease-out` | cubic-bezier(0.16, 1, 0.3, 1) | default |
| `--ease-in-out` | cubic-bezier(0.65, 0, 0.35, 1) | controlled |
| `--distance-sm` | 8px | hover lift |
| `--distance-md` | 16px | reveal |
| `--distance-lg` | 32px | hero (rare) |
| `--stagger-step` | 60–80ms | groups |

- Durations in CSS custom properties; JS reads from `getComputedStyle` or config.
- Stagger: cards/lists only, 60–80ms, never randomized (predictability + a11y).

## 7. Where animation is allowed (page map)

| Page/page area | Motion allowed | Level |
|---|---|---|
| Home hero | entrance + optional DigitalCore 3D (separate) | slow, purposeful |
| Home sections | scroll reveals (headings, grids) | base |
| Product detail hero/screenshots | entrance, gallery transitions | base |
| Demo panel | fade/slide on load, unless demo state changes | fast |
| Cards/lists (all) | hover lift, staggered reveal | fast/base |
| Nav/drawer/dialog | state transitions | fast |
| Blog/insights | minimal (reveal + TOC highlight) | base |
| Admin (Blue Control) | CSS transitions only (fast) | internal |

**Never animate:** text selection, form validation errors as sole indicator,
focus movement, content that carries meaning (must remain perceivable without motion).

## 8. Performance budgets (enforced at build)

| Budget | Target |
|---|---|
| Motion JS (core + reveal + nav + dialog) | ≤ 40 kB gz initial (excluding three) |
| Three.js chunk | loaded **only** when a 3D module is actually used |
| FPS | ≥ 50 on mid-range mobile with 3D; ≥ 60 for 2D reveals |
| Scroll listener count | ≤ 1 active per page (ScrollTrigger consolidated via one scroller) |
| Layout thrash | 0 forced reflows in rAF loop; use transform/opacity only |
| Long tasks | no > 50ms tasks in motion init |

## 9. Implementation phases

- **Phase 1:** tokens + reduced-motion baseline CSS; `Reveal` component with
  IntersectionObserver fallback (no GSAP yet); micro-interactions via CSS.
- **Phase 2:** introduce GSAP + ScrollTrigger + Lenis through MotionManager;
  hero + reveals + nav/drawer.
- **Phase 3:** product gallery/lightbox motion, case-study storytelling, counters
  (real data only).
- **Later:** choreographed hero scenes, lab/experimental motion, 3D (own module).
