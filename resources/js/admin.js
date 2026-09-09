/**
 * Blue Control JS entry (Vite input).
 * Shares the accessibility module; admin UI helpers cover confirms/filters.
 */

import { syncDirection } from './modules/core';
import { initDialogBehavior, initDropdowns } from './modules/accessibility';
import { initAdminUi } from './modules/admin-ui';

document.addEventListener('DOMContentLoaded', () => {
  syncDirection();
  initDialogBehavior();
  initDropdowns();
  initAdminUi();
});
