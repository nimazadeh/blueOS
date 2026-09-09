/**
 * Motion foundation (Phase 1B) — no animation libraries.
 *
 * - Fade/slide reveals via IntersectionObserver + CSS transitions.
 * - Hover micro-interactions are CSS-only (see components).
 * - `prefers-reduced-motion: reduce` → elements are revealed immediately
 *   (CSS also forces opacity/transform to normal values).
 * - Lifecycle: returns a destroy() that disconnects observers.
 */

const prefersReducedMotion = () =>
  window.matchMedia('(prefers-reduced-motion: reduce)').matches;

export function initMotion(root = document) {
  const targets = root.querySelectorAll('[data-reveal]');

  if (!targets.length) return { destroy: () => {} };

  if (prefersReducedMotion() || !('IntersectionObserver' in window)) {
    targets.forEach((el) => el.classList.add('is-revealed'));
    return { destroy: () => {} };
  }

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-revealed');
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.12, rootMargin: '0px 0px -5% 0px' },
  );

  targets.forEach((el) => observer.observe(el));

  return {
    destroy() {
      observer.disconnect();
    },
  };
}
