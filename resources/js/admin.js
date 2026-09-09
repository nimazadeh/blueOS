/**
 * Blue Control JS entry (Vite input).
 * Shares the accessibility module; Livewire interactivity lands later.
 */

import { syncDirection } from './modules/core';
import { initDialogBehavior, initDropdowns } from './modules/accessibility';

document.addEventListener('DOMContentLoaded', () => {
  syncDirection();
  initDialogBehavior();
  initDropdowns();
});
