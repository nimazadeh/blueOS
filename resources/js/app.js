/**
 * Public site JS entry (Vite input).
 *
 * Phase 1B bundle: motion (IntersectionObserver), navigation (accessible
 * drawer), accessibility helpers (dialog/dropdown). No animation or 3D
 * libraries — budget stays tiny (~2-3 kB gz).
 */

import { syncDirection } from './modules/core';
import { initLocaleSwitchers } from './modules/locale-switcher';
import { initMotion } from './modules/motion';
import { initNavigation } from './modules/navigation';
import { initDialogBehavior, initDropdowns } from './modules/accessibility';

document.addEventListener('DOMContentLoaded', () => {
  syncDirection();
  initLocaleSwitchers();
  initMotion();
  initNavigation();
  initDialogBehavior();
  initDropdowns();
});
