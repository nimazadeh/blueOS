# Blue Studio OS — Design System Foundation

**Status:** Accepted (Phase 0). Exact hex values and type scales are *direction*;
final refinement happens at the start of Phase 1 (design phase) before page
implementation begins.

---

## 1. Principles

1. **Premium but restrained.** Technical, minimal, immersive. No excessive gradients,
   glassmorphism, noise, or template aesthetics.
2. **Token-first.** Every color, space, radius, shadow, typography step, and motion
   value is a named token. No raw values in components.
3. **RTL-native.** Tokens and components are written direction-agnostic using CSS
   logical properties (`margin-inline-start`, `inset-inline-end`, `text-align: start`,
   `border-inline-start`, `padding-inline`, …). Persian is not a "flip".
4. **Accessible by default.** Contrast is checked against tokens; focus is always
   visible; motion honors `prefers-reduced-motion`.
5. **One system, one vocabulary.** Components (Button, Card, Section, Eyebrow, Badge,
   Dialog, FormField, …) are the only way to render UI; no ad-hoc styling in pages.

## 2. Color tokens

### 2.1 Direction (Phase 0 values — refine in Phase 1 design pass)

```
--color-bg-deep:        deep navy          (primary background; NEVER pure black)
--color-bg-surface:     blue-gray surface  (cards/panels, slightly lighter than bg)
--color-bg-elevated:    elevated surface   (overlays/popovers)
--color-accent:         bright blue        (primary action, links, focus ring)
--color-accent-hover:   lighter accent     (hover state)
--color-text-primary:   soft white         (headings, body)
--color-text-secondary: muted blue-gray    (supporting text)
--color-text-disabled:  low-contrast       (disabled states only)
--color-border:         subtle blue-gray   (borders, dividers)
--color-danger:         restrained red     (errors, destructive)
--color-warning:        amber              (pending/attention, used sparingly)
--color-success:        muted green        (live/active states)
```

### 2.2 Usage rules

- Backgrounds never pure black (`#000`); deep navy is the floor.
- Text on deep navy uses white at ≥ 4.5:1 contrast for body; display sizes may use
  ≥ 3:1 (large text) — still verify.
- Accent used for: primary CTAs, links, focus outlines, active states, key visuals.
  Never for large background fills (keeps it premium/restrained).
- Semantic states (live demo, draft, published) use badge tokens mapped to the palette,
  not raw colors.
- Dark theme is the default direction; light theme may arrive later as a token
  override layer **only if** it doesn't compromise the premium direction (deferred).

## 3. Spacing scale

```
--space-0:  0
--space-1:  4px    (2 tokens used between tight items)
--space-2:  8px
--space-3:  12px
--space-4:  16px
--space-5:  24px
--space-6:  32px
--space-7:  48px
--space-8:  64px
--space-9:  96px
--space-10: 128px  (section whitespace, used sparingly)
```

Rule: use steps only; no arbitrary values outside tokens. Section rhythm uses
`--space-8`/`--space-9`; card padding `--space-5`/`--space-6`.

## 4. Typography

### 4.1 Type scale (steps — final sizes in Phase 1)

```
--text-display:    clamp(2.5rem, 5vw, 4rem)    hero lines, weight 700, tight leading
--text-h1:         clamp(2rem, 4vw, 3.25rem)
--text-h2:         clamp(1.5rem, 3vw, 2.25rem)
--text-h3:         1.25–1.5rem
--text-body:       1rem–1.125rem, line-height 1.65+ (RTL needs taller leading)
--text-small:      0.875rem
--text-caption:    0.75rem, letter-spacing 0.08em, uppercase (LTR-safe: use
                   letter-spacing sparingly — Persian doesn't use uppercase/letterspacing)
--text-label:      0.8125rem, medium weight, tracking minimal
```

### 4.2 Families

- **Latin:** premium grotesque/system stack (e.g., Inter or a similar licensed/open
  face — final choice in Phase 1; must be self-hosted via npm, no CDN runtime dep).
- **Persian:** **Vazirmatn** (npm `vazirmatn` / `@fontsource/vazirmatn`, self-hosted).
- **Code/technical:** system mono (`ui-monospace, SFMono, Menlo, Consolas`), with
  `dir="ltr"` isolation for inline code.
- Font loading: `font-display: swap`, subsetted, `preload` critical face, no FOUT
  jank (see [`performance.md`](performance.md)).

### 4.3 RTL typography rules

- Persian doesn't use uppercase/wide tracking for labels; use weight + size for
  hierarchy in `fa` instead of letter-spacing.
- Line-height 1.7–1.8 for Persian body; descenders need room.
- Always set `dir="ltr"` + `unicode-bidi: isolate` on: product names, code, emails,
  URLs, version strings, `dir="auto"` where user text is displayed.
- Persian numerals: use `toLocaleString('fa-IR')`/intl only for editorial numbers;
  keep versions/URLs/technical numbers Latin (see [`accessibility.md`](accessibility.md)).

## 5. Radius

```
--radius-xs:  4px   (badges, small elements)
--radius-sm:  6px   (inputs, buttons)
--radius-md:  10px  (cards, panels)
--radius-lg:  16px  (large surfaces, hero panels)
--radius-full: 999px (pills, avatars)
```

Rule: consistent per element type; no random radii in components.

## 6. Shadows & borders

```
--shadow-sm: 0 1px 2px rgba(4, 12, 28, 0.35)             — hairline elevation
--shadow-md: 0 8px 24px rgba(4, 12, 28, 0.30)            — cards/hover
--shadow-lg: 0 24px 64px rgba(4, 12, 28, 0.40)           — modals/popovers
--border:    1px solid var(--color-border)
--border-strong: 1px solid color-mix(in srgb, var(--color-border) 70%, white 5%)
```

Restrained: shadows are depth cues, not decorations. No colored glows in v1.

## 7. Motion tokens (specified in [`animation-system.md`](animation-system.md))

```
--motion-fast:    150ms   micro-interactions
--motion-base:    300ms   component transitions
--motion-slow:    500ms   hero/entrance reveals
--ease-out:       cubic-bezier(0.16, 1, 0.3, 1)    — default (fast start, soft land)
--ease-in-out:    cubic-bezier(0.65, 0, 0.35, 1)   — controlled transitions
--distance-sm:    8px     — micro-shift
--distance-md:    16px    — standard reveal
--distance-lg:    32px    — hero reveal (used sparingly)
--stagger-step:   60–80ms — list/group reveals
```

## 8. Components vocabulary (conceptual v1 set)

- **Primitives:** `Button` (primary/secondary/ghost/danger; sizes), `IconButton`,
  `Badge` (status), `Eyebrow`, `Heading`, `Body`, `Label`, `Link`, `Divider`.
- **Layout:** `Section` (eyebrow/heading/body/CTA slots), `Container`, `Grid`,
  `Stack`, `Card` (media/body/footer variants), `Hero`, `PageHeader`, `Footer`,
  `Header/Nav`.
- **Content:** `MediaImage` (responsive variants), `Gallery`, `FeatureList`,
  `TechList`, `Callout`, `PullQuote`, `CodeBlock`, `Table`.
- **Forms:** `Field`, `Input`, `Textarea`, `Select`, `Checkbox`, `RadioGroup`,
  `FormError`, `HoneypotField`, `SubmitButton`.
- **Overlay:** `Dialog` (focus-trapped, ESC, scroll-lock, reduced-motion), `Drawer`
  (mobile nav), `Tooltip` (a11y-safe), `Toast` (status, not animation-only).
- **Motion wrappers:** `Reveal` (scroll trigger), `HoverLift`, `Ticker` (only where
  used), `Counter` (only real numbers, reduced-motion safe) — see animation docs.

## 9. Directives (rules that pages must obey)

1. No raw CSS values; use design tokens.
2. No `margin-left/right`, `padding-left/right`, `left/right`, `border-left/right`,
   `text-align: left/right`, `float` for layout — use logical properties
   (`*-inline-start/end`) so RTL is automatic.
3. Icons: directional icons (arrows, chevrons) must reflect direction. Use logical
   transforms (`scaleX(-1)` under `[dir="rtl"]`) or direction-aware icon components.
4. Focus: every interactive element has a visible 2px accent focus ring (`:focus-visible`),
   never `outline: none` without replacement.
5. Color contrast: verify tokens with automated tooling before merge (see
   [`qa.md`](qa.md)).
6. Text in mixed direction: use `dir="auto"` for user content, `dir="ltr"` + isolate
   for technical strings.
7. No placeholder/lorem content in real pages — only real content.
