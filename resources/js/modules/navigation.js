/**
 * Public navigation — mobile drawer with full keyboard support.
 *
 * Binds generic [data-drawer] components to their toggles via aria-controls:
 * - Toggle button: aria-expanded + aria-controls.
 * - Drawer: focus trap, Escape to close, close on link click, focus restore.
 * - Close automatically when resizing to desktop.
 */

const FOCUSABLE =
  'a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])';

function trapFocus(container, event) {
  if (event.key !== 'Tab') return;

  const focusables = container.querySelectorAll(FOCUSABLE);

  if (!focusables.length) return;

  const first = focusables[0];
  const last = focusables[focusables.length - 1];

  if (event.shiftKey && document.activeElement === first) {
    event.preventDefault();
    last.focus();
  } else if (!event.shiftKey && document.activeElement === last) {
    event.preventDefault();
    first.focus();
  }
}

export function initNavigation(root = document) {
  const toggle = root.querySelector('[data-nav-toggle]');

  if (!toggle) return { destroy: () => {} };

  // Generic drawer lookup: toggle points at it with aria-controls.
  const drawer = document.getElementById(toggle.getAttribute('aria-controls'));

  if (!drawer) return { destroy: () => {} };

  const isOpen = () => drawer.getAttribute('aria-hidden') === 'false';

  const open = () => {
    drawer.classList.add('drawer--open');
    drawer.setAttribute('aria-hidden', 'false');
    toggle.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';
    // Move focus into the drawer (close button comes first in DOM order).
    (drawer.querySelector(FOCUSABLE) || drawer).focus();
  };

  const close = (restoreFocus = true) => {
    drawer.classList.remove('drawer--open');
    drawer.setAttribute('aria-hidden', 'true');
    toggle.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
    if (restoreFocus) toggle.focus();
  };

  const onKeydown = (event) => {
    if (!isOpen()) return;

    if (event.key === 'Escape') {
      event.preventDefault();
      close();
      return;
    }
    trapFocus(drawer, event);
  };

  const onResize = () => {
    if (isOpen() && window.matchMedia('(min-width: 1280px)').matches) {
      close(false);
    }
  };

  toggle.addEventListener('click', () => {
    isOpen() ? close() : open();
  });

  drawer.addEventListener('click', (event) => {
    if (event.target.matches('[data-drawer-close]')) close();
    if (event.target.closest('a[href^="#"]')) close();
  });

  document.addEventListener('keydown', onKeydown);
  window.addEventListener('resize', onResize, { passive: true });

  return {
    destroy() {
      document.removeEventListener('keydown', onKeydown);
      window.removeEventListener('resize', onResize);
    },
  };
}
