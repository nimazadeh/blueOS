@props([
    'id' => null,
    'labelledby' => null,
    'title' => null,
])

{{--
    Generic drawer (overlay) — Phase 1B UI primitive.

    Hooks:
      [data-drawer]           root; aria-hidden=true until opened
      [data-drawer-close]     backdrop + close button (click closes)
    Initial focus and focus trap are handled by the consumer's controller
    (see resources/js/modules/navigation.js) or by accessibility.js.

    The opener must set aria-controls="{{ $id }}" and aria-expanded.
--}}
<div
    {{ $attributes->merge(['id' => $id, 'class' => 'drawer', 'role' => 'dialog', 'aria-modal' => 'true', 'aria-hidden' => 'true']) }}
    data-drawer
>
    <div class="drawer__backdrop" data-drawer-close aria-hidden="true"></div>

    <div class="drawer__panel" tabindex="-1">
        <div class="drawer__header">
            <h2 id="{{ $labelledby }}" class="text-h3">{{ $title ?? '' }}</h2>
            <button type="button" class="drawer__close" data-drawer-close aria-label="{{ __('blue.nav.close_menu') }}">✕</button>
        </div>

        {{ $slot }}
    </div>
</div>
