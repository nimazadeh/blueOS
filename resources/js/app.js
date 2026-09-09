/**
 * Public site JS entry (Vite input).
 *
 * Phase 1A: foundational boot only — no libraries, no motion, no 3D.
 * Module graph grows with features (motion in Phase 1B+, 3D later) through
 * dynamic imports; the initial bundle stays tiny.
 */

import { syncDirection } from './modules/core';
import { initLocaleSwitchers } from './modules/locale-switcher';

document.addEventListener('DOMContentLoaded', () => {
  syncDirection();
  initLocaleSwitchers();
});
