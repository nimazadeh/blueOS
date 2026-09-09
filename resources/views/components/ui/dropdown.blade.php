@props([
    'label' => 'Options',
    'id' => null,
])

{{--
    Generic dropdown menu — keyboard accessible.

    Hooks:
      [data-dropdown]             wrapper
      [data-dropdown-trigger]     button (aria-expanded, aria-haspopup)
      [data-dropdown-menu]        role="menu" container (hidden until open)
    Menu items must have role="menuitem" (use `.dropdown__item` class).
    Behavior (modules/accessibility.js): click toggle, outside click close,
    ArrowUp/Down, Esc + focus return.
--}}
<div class="dropdown" data-dropdown>
    <button
        type="button"
        class="button button--secondary dropdown__trigger"
        data-dropdown-trigger
        aria-haspopup="menu"
        aria-expanded="false"
        @if($id) aria-controls="{{ $id }}" @endif
    >
        {{ $label }}
        <span class="dropdown__caret" aria-hidden="true">▾</span>
    </button>

    <div id="{{ $id }}" class="dropdown__menu" data-dropdown-menu role="menu" hidden>
        {{ $slot }}
    </div>
</div>
