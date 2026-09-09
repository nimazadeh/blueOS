# Blue Studio OS — Accessibility Requirements

**Status:** Accepted (Phase 0). Accessibility is a product requirement, not a later
"a11y pass". Target: **WCAG 2.2 AA** minimum, with the spirit of AAA for motion,
focus, and form feedback.

---

## 1. Keyboard navigation

- Every interactive element reachable and operable by keyboard alone; logical
  tab order (no positive `tabindex` except where a custom widget requires roving
  tabindex).
- Skip link to main content (first focusable element).
- Dialogs/drawers: focus trap, initial focus set, ESC closes, focus restored on close.
- Menus: roving tabindex + arrow keys; dropdown opens on click *and* supports
  keyboard (Enter/Space), not hover-only.
- No keyboard traps; no scroll-jacking that breaks arrow-key scrolling.
- Focus never hidden: visible `:focus-visible` ring (2px accent, ≥3:1 contrast on
  background) on every focusable element; `outline: none` without replacement
  is a QA failure.

## 2. Semantics & screen readers

- Semantic HTML: `header`, `nav`, `main`, `section`, `article`, `footer`; one `h1`
  per page; heading hierarchy not skipped.
- Landmarks labeled (`aria-label` on nav/footer when multiple).
- Forms: labels associated (never placeholder-only); fieldset/legend for groups;
  errors linked via `aria-describedby`, announced politely (`role="alert"` or
  `aria-live`); success confirmations announced; `aria-required` + visual required.
- Images: meaningful `alt` from media metadata; decorative images `alt=""` +
  `aria-hidden`; icons `aria-hidden` with adjacent text; complex images (diagrams)
  have text alternative.
- Buttons vs links semantics correct (action vs navigation); no clickable `<div>`.
- Live regions for: form status, toasts, lead success (not animation-only).
- Table of contents/anchors accessible; skip links repeated in long articles.

## 3. Color & contrast

| Element | Requirement |
|---|---|
| Body text | ≥ 4.5:1 |
| Large text (≥24px/18.66px bold) | ≥ 3:1 |
| UI components/borders (focus rings, boundaries) | ≥ 3:1 |
| Status colors (badges) | not the only indicator — include text/icon |
- Token palette validated with contrast tooling in CI (Phase 2 QA) — failing tokens
  block merge.
- Links distinguishable (underline or icon) — not color-only.

## 4. Movement & motion

Baseline (see [`animation-system.md`](animation-system.md)):

- `prefers-reduced-motion: reduce` → no auto-playing, no parallax, no scrolling
  reveals beyond static show, Lenis off, Three.js off (static fallback).
- Motion duration 150–500ms; no flashes above 3/sec (WCAG 2.3.1).
- Content changes announced via DOM, not animation; animation is never the only
  state indicator (spinner + text; toast + aria-live).

## 5. Forms & validation

- Errors inline next to field + summary at top (link to each), `aria-invalid`,
  `aria-describedby`.
- Focus moves to first error on submit.
- Success: confirmation page/state + `aria-live` announcement; PRG prevents
  duplicate submit.
- Rate-limit/error states: plain-language, locale-aware messages.
- Captcha: none in v1 (honeypot + rate limit); if added later, accessible
  alternative required (no image captcha without audio/alt option).

## 6. Responsive & RTL a11y notes

- Touch targets ≥ 44×44 CSS px (interactive), 24px min for inline links with
  adequate spacing.
- Text reflow at 400%/200% zoom without loss; no horizontal scroll on mobile
  (unless data table with scrollable region + label).
- RTL: `dir` correct on html; logical properties; mixed-direction tech strings
  isolated (`dir="ltr"` + `unicode-bidi: isolate`); screen reader order matches
  visual order; Persian numerals only where user-facing editorial (versions/URLs
  stay Latin).
- Focus order follows visual order in both LTR/RTL (logical properties prevent
  inverted tab order).

## 7. Content & language

- `lang` attribute on html per locale (`fa-IR` for Persian).
- Language changes announced via `lang` on sub-elements (`<span lang="en">` for
  product names inside Persian text).
- Abbreviations/technical acronyms explained first use; code snippets labeled.
- Plain language; no text-only-meaningful icons; no emoji-only indicators.
- Headings describe content; links have discernible text (no "click here").

## 8. Testing gates (integrated with [`qa.md`](qa.md) and [`testing.md`](testing.md))

- **Automated:** axe-core (Playwright axe scanner) on all public templates + admin
  login/dashboard; contrast checks in CI; focus-order smoke script.
- **Manual checklist per release:** keyboard-only walkthrough of: home → product →
  demo CTA → start project (submit) → success; admin login → lead view → edit →
  logout; dialogs/drawers; mobile RTL (fa) full flow.
- **Screen reader smoke:** NVDA/VoiceOver on key flows (manual, release gate).
- Known-issues list tracked in ADR-adjacent QA notes; no silent a11y regressions.

## 9. Enforcement in Phase 1

- Design tokens ship with required contrast pairs (verified by script).
- Base components ship with focus, aria, keyboard patterns (Button, Field, Dialog,
  Drawer, Tabs, MediaImage) — no "add a11y later" on base components.
- Accessibility lint in CI: eslint-plugin-jsx-a11y not applicable (Blade) — use
  Playwright axe + custom Blade assertions + stylelint contrast rules where possible.
