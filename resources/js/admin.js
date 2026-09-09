/**
 * Blue Control JS entry (Vite input).
 *
 * Phase 1A: placeholder entrypoint proving the split. Admin interactivity
 * (Livewire-driven) arrives with the Blue Control phase.
 */

import { syncDirection } from './modules/core';

document.addEventListener('DOMContentLoaded', () => {
  syncDirection();
});
