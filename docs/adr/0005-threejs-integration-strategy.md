# ADR-005 — Three.js Integration Strategy

- **Status:** Accepted
- **Date:** 2026-09-09
- **Supersedes:** none

## Context

The brief allows 3D "only where it provides meaningful value", requires the site to
remain usable without WebGL, and forbids implementing 3D in Phase 0. We must define
an architecture that keeps 3D isolated, lazy, performant, and fallback-safe.

## Decision

- Three.js exists only as **isolated lazily-loaded modules** (`resources/js/three/`)
  with **per-page opt-in via data attributes** and capability checks.
- Mounting order: idle → reduced-motion check → WebGL support (webgl2/webgl) →
  device tier (high/medium/low) → in-viewport check → dynamic import of module →
  SceneManager runs → on any failure, **designed static fallback** remains.
- Full lifecycle: resize (DPR-capped), pause off-screen/visibility-hidden, destroy
  (dispose geometries/materials/textures/renderer, force context loss).
- Modules (conceptual): DigitalCore, ProductScene, InteractiveShowcase, LabScene.
- No global `three` import; no 3D in main bundle; no scroll-jacking; canvas
  `aria-hidden`/decorative default; all meaning exists in HTML.
- Budgets: module chunk ≤180 kB gz; 0 bytes until used; ≥50 FPS mobile low tier;
  fallback within 300 ms.

## Alternatives considered

1. **Default 3D hero on every page** — rejected: violates "meaningful value" rule
   and performance budget.
2. **Static 3D images instead** — considered as the fallback design; accepted as
   part of the fallback contract, not as a replacement for capability.
3. **Babylon.js / R3F** — rejected: Three.js familiarity + size; R3F implies React
   (incompatible with Blade).
4. **CSS 3D/WebGL hybrid** — deferred; can be used in static fallback design
   without Three.js dependency.
5. **Install Three.js now** — rejected (Phase 0 rule 38).

## Consequences

- ✅ Public site stays usable/fast without WebGL, with motion reduced, or on mobile.
- ✅ 3D can be added per-experience without touching site architecture.
- ⚠️ Only meaningful if the studio invests in module + fallback design; otherwise
  hero stays static (acceptable outcome).
- ⚠️ Deterministic cleanup code required — reviewed at module implementation time.
