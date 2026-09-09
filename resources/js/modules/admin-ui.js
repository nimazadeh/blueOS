/**
 * Blue Control UI helpers (no inline handlers — CSP-friendly).
 *
 * - [data-confirm] forms: confirm before submit (deletion, status changes).
 * - [data-auto-submit] selects: submit their parent form on change (filters).
 */

export function initAdminUi(root = document) {
  const cleanups = [];

  // Repeating feature rows: clone the template, fill the index placeholder.
  const addFeature = root.querySelector('[data-add-feature-row]');

  if (addFeature) {
    const container = root.querySelector('#product-features');
    const template = root.querySelector('#product-feature-row-template');

    if (container && template) {
      const add = () => {
        const index = container.children.length;
        const fragment = template.content.cloneNode(true);
        fragment.innerHTML = fragment.innerHTML.replaceAll('INDEX', String(index));
        container.appendChild(fragment);
      };

      addFeature.addEventListener('click', add);
      cleanups.push(() => addFeature.removeEventListener('click', add));
    }
  }

  root.querySelectorAll('form[data-confirm]').forEach((form) => {
    const message = form.dataset.confirm || 'Are you sure?';

    const onSubmit = (event) => {
      if (!window.confirm(message)) {
        event.preventDefault();
      }
    };

    form.addEventListener('submit', onSubmit);
    cleanups.push(() => form.removeEventListener('submit', onSubmit));
  });

  root.querySelectorAll('select[data-auto-submit]').forEach((select) => {
    const onChange = () => select.form?.requestSubmit();
    select.addEventListener('change', onChange);
    cleanups.push(() => select.removeEventListener('change', onChange));
  });

  return {
    destroy() {
      cleanups.forEach((fn) => fn());
    },
  };
}
