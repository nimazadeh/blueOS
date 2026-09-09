/**
 * Locale switcher — submits the surrounding form (POST /locale/{locale}).
 * Works without JS too (it is a regular form); this module only removes the
 * need for a visible submit control in future builds.
 */

export function initLocaleSwitchers(root = document) {
  const switchers = root.querySelectorAll('[data-locale-switcher]');

  switchers.forEach((form) => {
    if (form.dataset.localeSwitcherInitialized) return;
    form.dataset.localeSwitcherInitialized = 'true';
  });
}
