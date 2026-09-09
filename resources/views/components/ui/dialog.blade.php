@props([
    'id' => null,
    'labelledby' => null,
    'title' => null,
])

{{--
    Generic dialog (modal) — accessible overlay primitive.

    Hooks:
      [data-dialog]               root; aria-hidden=true until opened
      [data-dialog-open="id"]     opener (see accessibility.js)
      [data-dialog-close]         close button + backdrop
    Behavior (modules/accessibility.js): open/close, Esc, focus restore,
    role="dialog" aria-modal="true" aria-labelledby wiring.
--}}
<div
    {{ $attributes->merge(['id' => $id, 'class' => 'dialog', 'role' => 'dialog', 'aria-modal' => 'true', 'aria-labelledby' => $labelledby, 'aria-hidden' => 'true']) }}
    data-dialog
>
    <div class="dialog__backdrop" data-dialog-close aria-hidden="true"></div>

    <div class="dialog__panel" tabindex="-1">
        <button type="button" class="dialog__close" data-dialog-close
                aria-label="{{ __('blue.close') }}">✕</button>

        @if($title)
            <h2 id="{{ $labelledby }}" class="dialog__title">{{ $title }}</h2>
        @endif

        {{ $slot }}
    </div>
</div>
