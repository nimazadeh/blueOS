# Blue Studio OS — Design System (Phase 1B)

**Status:** Implemented (Phase 1B). The Phase 0 direction document has been
superseded by the landed, tokenized system described here. All values below are
the ones actually compiled into `public/build`; there is no "direction-only"
table anymore.

---

## 1. Principles

1. **Premium but restrained.** Technical, minimal, immersive. No excessive
   gradients, glassmorphism, noise, or template aesthetics. Dark foundation
   (`#0f1220`), bright blue accent, disciplined whitespace.
2. **Token-first.** Every color, space, radius, shadow, typography step and
   motion value is a named token under `resources/scss/tokens/`. No raw values
   in components (enforced by the CSS audit in CI/docs).
3. **RTL-native.** Components are written direction-agnostic using CSS logical
   properties (`margin-inline-start`, `inset-inline-end`, `text-align: start`, …).
   Persian is not an afterthought "flip": typography, motion direction and
   spacing tokens adjust under `[dir='rtl']` (ADR-013: no uppercase, no wide
   letter-spacing for Persian; taller leading).
4. **Accessible by default.** Contrast is verified against token pairs
   (see §5), focus is always visible, motion honors `prefers-reduced-motion`.
5. **One system, one vocabulary.** Components under `resources/views/components/`
   are the only way to render UI; pages compose them, they never style ad-hoc.

## 2. Color tokens (`tokens/_colors.scss`)

Dark theme is the default (`:root`); the light theme is complete and applies
via `[data-theme='light']` on `<html>`. Never pure black.

| Token | Dark | Light |
|---|---|---|
| `--color-bg-deep` | `#0f1220` | `#f5f6fa` |
| `--color-bg-surface` | `#171b2c` | `#ffffff` |
| `--color-bg-elevated` | `#1e2336` | `#f0f2f8` |
| `--color-bg-inset` | `#12151f` | `#ffffff` |
| `--color-accent` (links/icons) | `#6c92ff` | `#2a5ae2` |
| `--color-accent-hover` | `#8fb3ff` | `#1f56e3` |
| `--color-accent-surface` (filled buttons) | `#3767f0` | `#2857d8` |
| `--color-accent-surface-hover` | `#2f66f4` | `#1f56e3` |
| `--color-accent-soft` | rgb(108 146 255 / .14) | rgb(42 90 226 / .10) |
| `--color-on-accent` | `#ffffff` | `#ffffff` |
| `--color-text-primary` | `#eef2f9` | `#0f1220` |
| `--color-text-secondary` | `#9aa7bd` | `#4b566b` |
| `--color-text-disabled` | `#5d6b82` | `#98a1b3` |
| `--color-border` | `#262d42` | `#dde2ec` |
| `--color-border-strong` | `#39415c` | `#c3cadd` |
| `--color-danger` / hover | `#f2555a` / `#ff7075` | `#d93843` / `#c02b36` |
| `--color-warning` | `#f5a524` | `#b87408` |
| `--color-success` | `#3dd68c` | `#1f7a4d` |
| `--color-focus-ring` | `#8fb3ff` | `#1f56e3` |
| `--color-scrim` | rgb(6 9 18 / .72) | rgb(15 18 32 / .34) |

Notice the **accent / accent-surface split**: links and icons use the brighter
accent (AA against the page background), while filled action surfaces use the
darker surface token so white label text stays AA in BOTH themes. This was a
deliberate correction found during the contrast audit — the original single
accent failed white-on-accent at 3.73:1 in dark mode.

Semantic tone tokens (`danger`, `warning`, `success`) each ship a `-soft`
companion for tinted badges; badges with soft tones never carry white text.

## 3. Typography (`tokens/_typography.scss`, `fonts.scss`)

- **Latin:** Inter Variable (wght 100–900), self-hosted
  (`resources/fonts/inter-latin-wght-normal.woff2`, 48.25 kB), `font-display: swap`.
- **Persian:** Vazirmatn 400/700 Arabic subset, self-hosted (21.08 + 21.72 kB).
- Stacks: `--font-sans` (Inter), `--font-sans-fa` (Vazirmatn first), `--font-mono`.
- Scales: `--text-display` clamp(2.75→4.5rem), `--text-h1` clamp(2.25→3.5rem),
  `--text-h2` clamp(1.75→2.5rem), `--text-h3` clamp(1.375→1.75rem);
  body `--text-body-large`, `--text-body`, `--text-small`, `--text-caption`,
  `--text-label`.
- Weights 400/500/600/700; leading tight 1.15 / snug 1.35 / normal 1.6
  (1.75 in RTL) / relaxed 1.8 (1.9 in RTL).
- Letter-spacing (`-0.025em` display/H1/H2, `0.08em` captions/eyebrows) is
  **Latin only** — `[dir='rtl']` zeroes it; Persian relies on weight.
- Utilities: `.text-display`, `.text-h2`, `.text-h3`, `.text-body-large`,
  `.text-small`, `.text-caption`, `.text-label`, `.eyebrow`.

## 4. Space, radius, shadow, motion, breakpoints

- Spacing scale (Phase 1A) in `tokens/_spacing.scss`; containers clamp via
  `--container-max`, gutters via `--container-gutter`.
- Radius: `--radius-sm/md/lg/pill` in `tokens/_radius.scss` (shipped from 1A).
- Shadows in `tokens/_shadows.scss`: subtle, level-based, no giant glows.
- Motion: `tokens/_motion.scss` durations/easings + `utilities/_motion.scss`
  reveals (see §7). Values honor `prefers-reduced-motion`.
- Breakpoints (`tokens/_breakpoints.scss`): 320 / 768 / 1280 / 1440 — as SCSS
  map **and** CSS variables; header nav switches to the mobile drawer below
  1280px.

## 5. Accessibility & contrast verification

WCAG 2.2 AA is a hard requirement. Token pairs are verified by scripted ratio
calculation (docs/phase-1b-report.md §Results records the 10-pair matrix):

| Pair | Ratio | Verdict |
|---|---|---|
| Dark: white on `accent-surface` | 4.82:1 | AA |
| Dark: white on `accent-surface-hover` | 4.85:1 | AA |
| Dark: `accent` on `bg-deep` | 6.38:1 | AA |
| Dark: `text-primary` on `bg-deep` | 16.59:1 | AAA |
| Dark: `text-secondary` on `bg-surface` | 7.02:1 | AA |
| Light: white on `accent-surface` | 6.10:1 | AA |
| Light: white on `accent-surface-hover` | 6.00:1 | AA |
| Light: `accent` on `bg-deep` | 5.31:1 | AA |
| Light: `text-primary` on `bg-deep` | 17.24:1 | AAA |
| Light: `text-secondary` on `bg-deep` | 6.84:1 | AA |

Focus: 3px `--color-focus-ring` outline, 2px offset, on `:focus-visible` for
every interactive component. Skip link, `aria-expanded`/`aria-controls`
drawer, labelled sections (`aria-labelledby`), `prefers-reduced-motion`
safety net in `base/_accessibility.scss`.

## 6. Component API

All components (Button, Badge, Card, Form controls, Dialog, Drawer, Dropdown,
Tooltip, EmptyState, SectionHeading, plus domain shells ProductCard,
ServiceCard, CaseStudyCard) are built from these tokens. Their exact contracts
and states live in `docs/components.md`.

## 7. Motion

A single, deliberately small system (`utilities/_motion.scss` +
`modules/motion.js`):

- `[data-reveal]` → opacity 0 + translateY(12px); `.is-revealed` added by
  IntersectionObserver (threshold 0.12, once).
- RTL-aware slide variants (`data-reveal="slide-start"/"slide-end"`) flip in
  `[dir='rtl']`.
- Hover micro-interactions are transform/color only, GPU-cheap, no layout
  thrash (buttons, cards, links).
- `prefers-reduced-motion: reduce` forces opacity 1/transform none and the JS
  reveals immediately.
- No GSAP, no Three.js, no animation libraries (reserved for later phases).
