/**
 * Core boot helpers — Phase 1A foundation.
 *
 * Progressive enhancement only: the site is fully usable without JS.
 * Motion, 3D and advanced modules are deferred (see docs/animation-system.md).
 */

/** Expose direction/locale on documentElement for CSS hooks. */
export function syncDirection() {
  const body = document.body;
  if (!body) return;

  const direction = body.dataset.direction;
  if (direction === 'rtl' || direction === 'ltr') {
    document.documentElement.setAttribute('dir', direction);
  }

  const locale = body.dataset.locale;
  if (locale) {
    document.documentElement.setAttribute('lang', locale.replace('_', '-'));
  }
}
