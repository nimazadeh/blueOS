# ADR-009 — Asset Build Tooling

- **Status:** Accepted
- **Date:** 2026-09-09
- **Supersedes:** none

## Context

Laravel needs a modern asset pipeline compatible with Blade SSR, capable of SCSS,
ES modules, dynamic imports (motion/3D), per-locale font loading, and source maps
in dev — with deliberate dependency policy (no fashionable additions).

## Decision

- **Vite** (`vite` 8.x) + **laravel-vite-plugin** with multiple entries:
  `app` (public core), `motion` (dynamic), `three/*` (per-module dynamic), `admin`
  (Blue Control only).
- **SCSS** (dart-sass, modern API) token-first architecture; no Tailwind in v1
  (bespoke design system via tokens; Tailwind requires an ADR if reconsidered).
- **TypeScript**: used **only where it improves reliability** — the three.js layer
  and any integration-heavy utility; public page JS stays ES modules (plain JS)
  unless a module's complexity justifies TS. TypeScript 5.x LTS line (not 7.x until
  ecosystem stable, documented).
- All assets self-hosted through Vite (no external CDN/font CDN); fingerprint +
  immutable cache in prod.
- Deliberate dependencies only (GSAP, Lenis, Alpine, Three — each justified in
  [`../architecture.md`](../architecture.md#8-dependency-policy)); no UI framework.

## Alternatives considered

1. **Laravel Mix (webpack)** — deprecated; rejected.
2. **Tailwind** — rejected for bespoke token system; possible future ADR if design
   velocity demands.
3. **esbuild direct** — viable but duplicates Vite's HMR/plugin ecosystem; rejected.
4. **pnpm** — fine but not installed; npm 10 suffices (documented in environment.md).
5. **TypeScript everywhere** — rejected per brief: TS only where it pays.

## Consequences

- ✅ Modern, fast builds; split entries keep public JS lean; self-hosted assets.
- ✅ Motion/3D stay out of the critical path via dynamic imports.
- ⚠️ Multiple entries + manual chunk config need maintenance (documented).
- ⚠️ Developer must know Vite + SCSS conventions; tooling is greenfield (no legacy).
