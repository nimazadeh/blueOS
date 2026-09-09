# Blue Studio OS — Three.js Architecture

**Status:** Accepted (Phase 0). **No 3D is implemented in Phase 0.** This document
defines how 3D will be integrated when a future phase builds it, so that the public
site remains fast, usable, and accessible whether or not WebGL exists.

---

## 1. Decision (ADR-005)

Three.js is an **isolated, lazily-loaded, optionally-present** layer inside the
frontend architecture. It is not a design requirement for any page; it is a
capability that specific experiences (conceptually: DigitalCore, ProductScene,
InteractiveShowcase, LabScene) may use when they add value.

## 2. Rule: 3D only where it provides meaningful value

| Potential module | Where | Value it must earn |
|---|---|---|
| **DigitalCore** | Home hero candidate | brand identity/story — only if it outperforms a static hero in clarity and doesn't delay first paint |
| **ProductScene** | Product detail (optional, per product flag) | communicating the product's shape/structure better than screenshots |
| **InteractiveShowcase** | product/lab featured section | interactive exploration (rare; gated by device + reduced-motion) |
| **LabScene** | Lab experimental pages | R&D/experimental identity — most natural fit for 3D experimentation |

Each module is a separate entry chunk and is **opt-in per page via
`data-3d="module-name"` + capability checks**. No 3D on forms, blog, admin, or
listing pages.

## 3. Module architecture

```
resources/js/three/
├── config.js            — capability detection, module manifest, paths
├── core/
│   ├── Renderer.js      — WebGL renderer factory (antialias, pixelRatio cap)
│   ├── SceneManager.js  — scene lifecycle, RAF loop, resize, cleanup
│   ├── CameraRig.js     — camera + optional pointer parallax
│   └── Loaders.js       — GLTF/texture loading (lazy, no blocking)
├── modules/
│   ├── DigitalCore.js
│   ├── ProductScene.js
│   ├── InteractiveShowcase.js
│   └── LabScene.js
└── index.js             — entry: called ONLY after dynamic import resolves
```

### Loading strategy

```
Page HTML contains <div data-3d="digital-core" data-3d-fallback="static-hero">
  → no JS runs for 3D at initial parse
  → on idle (requestIdleCallback / load event) MotionManager checks:
      • module enabled for this page?
      • shouldReduceMotion()?  → skip (render static fallback)
      • WebGL support? canvas.getContext('webgl2')||('webgl')
      • device tier (mobile/tablet/desktop + DPR) → quality preset
      • intersection? only mount when hero is in viewport
  → dynamic import('../../three/modules/DigitalCore.js') (separate chunk)
  → module mounts → SceneManager runs → reports readiness
  → on failure at any step: FAIL SILENT = static fallback remains; no error UI,
    no blocking, no console spam beyond one debug log
```

### Quality presets (device capability)

| Preset | DPR cap | Geometry/texture | Effects |
|---|---|---|---|
| `high` (desktop) | 2 | full | post pass if used |
| `medium` (tablet) | 1.5 | medium | reduced particles |
| `low` (mobile) | 1 | low | no post-processing, simplified materials, capped pixel ratio |
| `off` | — | — | static fallback |

Checks gated: `matchMedia('(pointer: fine)')`, `navigator.hardwareConcurrency`,
`devicePixelRatio`, WebGL renderer capability (fail on mobile GPU blacklist → fallback).

## 4. Lifecycle & cleanup

```js
class SceneManager {
  mount(container, module) { /* create renderer, scene, module, RAF */ }
  resize()                  { /* ResizeObserver; capped at 2x DPR; recomp only when needed */ }
  pause()                   { /* IntersectionObserver — stop RAF when off-screen */ }
  destroy()                 { /* cancel RAF, remove listeners, dispose geometries/materials/
                                 textures/renderer, force GPU memory release via
                                 renderer.dispose() + forceContextLoss() */ }
}
```

- **Destroy is mandatory:** no memory leaks across route/page changes; no WebGL
  context leakage (browsers cap ~16 contexts).
- **Pause on off-screen:** expensive scene never renders when not visible.
- **Orientation/resize:** debounced; no full re-init.
- **Visibility:** `document.visibilitychange` pauses RAF.
- **Livewire/admin:** 3D never initialized there.

## 5. Fallback contract

| Condition | Behavior |
|---|---|
| `prefers-reduced-motion: reduce` | static fallback (hero image/illustration/HTML composition) |
| No WebGL (or context creation failed) | same static fallback |
| Module fails to load (network/error) | catch → fallback |
| WebGL context lost mid-session (`webglcontextlost`) | fallback + clear container + log |
| Mobile data saver / low-power | `save-data` header check → fallback or low preset |

**Fallback must be first-class** — pre-designed in Phase 1 (component/visual), never
"leftover markup behind a canvas". Every 3D section ships a designed static version.

## 6. Accessibility & interaction

- 3D is **decorative by default** (`aria-hidden="true"` + `role="presentation"` on
  canvas container); all content information also exists in HTML text, never only in
  canvas.
- Interactive 3D (if ever built) must have keyboard-accessible alternative controls
  or be excluded from critical content.
- No autoplay/looping motion without pause control and reduced-motion gate.
- Never use canvas as a link target; interactive hotspots are real DOM elements.
- Focus never enters canvas; canvas controls bypassed by keyboard users.

## 7. Performance budget (3D layer)

| Metric | Target |
|---|---|
| Chunk size (three + module) | ≤ 180 kB gz total; split per module |
| Initial load impact | 0 bytes until idle + in-view |
| FPS | ≥ 50 mid-tier mobile (low preset), ≥ 60 desktop |
| GPU memory | ≤ 256 MB estimate; dispose always |
| Layout impact | canvas container has fixed aspect-ratio (no CLS) |
| First paint | never blocked by 3D (module loads after LCP-critical resources) |
| Failure latency | < 300ms to fallback |

## 8. What must NOT happen

- No global `three` import in the main bundle.
- No 3D on pages where it competes with conversion (lead forms, checkout-like flows).
- No scroll-jacking through 3D camera.
- No per-frame DOM writes (transform canvases only, with `will-change` contained).
- No WebGL scene as sole representative of meaningful content.

## 9. Phase plan

- **Phase 0 (now):** architecture only. ✅
- **Phase 1:** static fallback components designed + tokens; no three.js installed.
- **Phase 2:** motion system core; still no three.
- **Phase 3/4:** evaluate DigitalCore on home hero against static alternative;
  build only if measurable improvement (task timing: LCP ≤ 2.5s, no layout shift).
- **Later:** ProductScene/LabScene behind per-product flags.

## 10. Acceptance for any future 3D work

- [x] Isolated chunk + lazy + idle-gated
- [x] Capability/reduced-motion/mobile checks
- [x] Designed static fallback
- [x] Full destroy/cleanup + context-loss handling
- [x] A11y rules respected
- [x] Performance measured against §7 and [`performance.md`](performance.md)
