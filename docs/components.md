# Blue Studio OS — Components (Phase 1B)

Every component below is server-rendered Blade under
`resources/views/components/`, styled exclusively by the token-driven SCSS in
`resources/scss/`, and — where interactive — wired by the ES modules in
`resources/js/modules/`.

**Universal guarantees** (checked per component):

- RTL: logical properties only; `[dir='rtl']` overrides where direction is
  intrinsic (select arrow, drawer slide, dropdown origin).
- Keyboard: Tab order is DOM order; overlays trap focus; Esc closes.
- Hover/focus states use tokens (`--color-accent-surface-hover`,
  `--color-focus-ring`) — never ad-hoc colors.
- Responsive: fluid type/container; drawer below 1280px; grids collapse.
- Dark + light: only token references; no theme-specific component forks.

---

## `x-ui.button`

`<x-ui.button :href="$url" variant="primary|secondary|ghost|danger" size="sm|lg" loading disabled>`

Renders an `<a>` when `href` is set, `<button>` otherwise. States: default,
hover, focus-visible, disabled, loading (spinner + `aria-busy`; text kept in
DOM but visually hidden). Primary fills with `accent-surface` (AA white text
in both themes).

## `x-ui.badge`

`<x-ui.badge tone="accent|success|warning|danger" :dot="true">Label</x-ui.badge>`

Tinted (`-soft` background) status chip. Dot is decorative
(`aria-hidden`); meaning must be in the label itself.

## `x-ui.card` (content wrapper)

`<article class="card">` with `card__media` / `card__body` slots; used by
domain cards (ProductCard, CaseStudyCard) and service blocks. Hover elevation
is transform-only.

## `x-ui.section-heading`

`align="start|center"`, `eyebrow`, `title`, `description`. Always emits
`aria-labelledby`-paired heading so sections are programmatically labelled.

## `x-ui.empty-state`

`title`, `description`, optional `action` slot. Used wherever content is
legitimately absent (products, portfolio in Phase 1B) — **never fake
content**.

## `x-ui.drawer`

`id`, `labelledby`, `title`, slot. Overlay panel with scrim, close button
(`data-drawer-close`), `role="dialog" aria-modal="true"`,
`aria-hidden` lifecycle. Initial focus + trap + Esc handled by
`modules/navigation.js` via the opener's `aria-controls`. Used by the public
header for the mobile nav; reusable for any overlay panel.

## `x-ui.dialog`

`<x-ui.dialog id labelledby title>` — overlay with scrim
(`data-dialog-close`), close button, `role="dialog" aria-modal="true"`,
`aria-labelledby`. Opened by any element with
`data-dialog-open="{{ $id }}"`; behavior in `modules/accessibility.js`:
focus moves in on open, Esc + backdrop + close button hide, focus restored
to the opener. Centering is RTL-correct (mirrored translate).

## `x-ui.dropdown`

`<x-ui.dropdown label>` with `role="menuitem"` items in the slot.
Hooks: `[data-dropdown-trigger]` + `[data-dropdown-menu]`; behavior in
`modules/accessibility.js`: click toggle, outside-click close, ArrowUp/Down,
Esc + focus return, `aria-expanded` synced.

## `x-ui.tooltip`

`<x-ui.tooltip text>trigger</x-ui.tooltip>` — CSS-driven accessible tooltip
(`:hover` + `:focus-within` reveal, `aria-describedby` on the trigger,
`role="tooltip"` bubble, `prefers-reduced-motion` honored). No JS needed;
hides when focus leaves. `placement="top|bottom"`.

## `x-public.header`

Sticky premium header: logo → home, desktop nav (Products / Portfolio /
Services anchors) ≥1280px, primary CTA (`#start-project`), hamburger +
accessible drawer below 1280px. Real anchors only — no broken or fake
destinations.

## `x-public.footer`

Brand block + tagline, real section navigation, services list from
`config('blue.services.preview')` (localized per locale), locale switcher
form, copyright. No social placeholders yet (no real destinations exist).

## Domain presentation components (no DB dependency)

| Component | Data contract | Phase 1B behavior |
|---|---|---|
| `x-products.product-card` | `$product` keys: `title`, `summary`, `href?` | Used by `featured-list` |
| `x-products.featured-list` | `$products` collection (empty → empty state) | Homepage section |
| `x-services.service-card` | `icon`, `title`, `description` | Icon + title + description |
| `x-services.featured-grid` | `$services` array from config | 6 real offerings |
| `x-portfolio.case-study-card` | `$project` keys: `cover?`, `client?`, `title`, `summary?` | Empty-state only |
| `x-portfolio.preview-list` | `$projects` collection (empty → empty state) | Homepage section |

Contracts are **presentation-only**: views receive prepared data from the
controller/domain; they never query models, never touch repositories, never
render CRUD.
