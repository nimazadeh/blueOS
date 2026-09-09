/**
 * Accessibility helpers (Phase 1B).
 * - Generic [data-dialog] behavior: open/close, focus management, Esc.
 * - [data-dropdown] menus: Enter/Space/Escape/ArrowKeys, click-outside.
 * - Focus restore on close.
 */

const FOCUSABLE = 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

export function initDialogBehavior(root = document) {
  const dialogs = root.querySelectorAll('[data-dialog]');
  const cleanups = Array.from(dialogs).map((dialog) => {
    const opener = root.querySelector(`[data-dialog-open="${dialog.id}"]`);
    if (!opener) return null;

    const open = () => {
      dialog.setAttribute('aria-hidden', 'false');
      dialog.classList.add('dialog--open');
      const first = dialog.querySelector(FOCUSABLE);
      if (first) first.focus();
    };

    const close = () => {
      dialog.setAttribute('aria-hidden', 'true');
      dialog.classList.remove('dialog--open');
      opener.focus();
    };

    opener.addEventListener('click', open);

    dialog.addEventListener('click', (event) => {
      if (event.target.closest('[data-dialog-close]')) close();
      if (event.target === dialog) close(); // backdrop click
    });

    const onKeydown = (event) => {
      if (event.key === 'Escape' && dialog.getAttribute('aria-hidden') === 'false') close();
    };
    document.addEventListener('keydown', onKeydown);

    return () => document.removeEventListener('keydown', onKeydown);
  });

  return {
    destroy() {
      cleanups.forEach((fn) => fn && fn());
    },
  };
}

export function initDropdowns(root = document) {
  const dropdowns = root.querySelectorAll('[data-dropdown]');

  const cleanups = Array.from(dropdowns).map((dropdown) => {
    const trigger = dropdown.querySelector('[data-dropdown-trigger]');
    const menu = dropdown.querySelector('[data-dropdown-menu]');
    if (!trigger || !menu) return null;

    const items = () => Array.from(menu.querySelectorAll('[role="menuitem"]'));

    const open = () => {
      menu.hidden = false;
      trigger.setAttribute('aria-expanded', 'true');
    };

    const close = () => {
      menu.hidden = true;
      trigger.setAttribute('aria-expanded', 'false');
    };

    trigger.addEventListener('click', () => {
      menu.hidden ? open() : close();
    });

    document.addEventListener('click', (event) => {
      if (!dropdown.contains(event.target)) close();
    });

    menu.addEventListener('keydown', (event) => {
      const list = items();
      const index = list.indexOf(document.activeElement);

      if (event.key === 'ArrowDown' && list.length) {
        event.preventDefault();
        list[(index + 1) % list.length].focus();
      } else if (event.key === 'ArrowUp' && list.length) {
        event.preventDefault();
        list[(index - 1 + list.length) % list.length].focus();
      } else if (event.key === 'Escape') {
        event.preventDefault();
        close();
        trigger.focus();
      }
    });

    return () => close();
  });

  return {
    destroy() {
      cleanups.forEach((fn) => fn && fn());
    },
  };
}
