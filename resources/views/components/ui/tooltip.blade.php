@props([
    'id' => null,
    'text' => null,
    'placement' => 'top',   // top | bottom (positioned by SCSS)
])

{{--
    Accessible tooltip — CSS-driven (hover + :focus-within), no JS needed.

    The slot is the trigger content (e.g. a "?" icon or a term); `text` is
    the tooltip copy. The trigger gets aria-describedby pointing at the
    bubble (role="tooltip"), so screen readers announce it on focus.
    Hiding happens naturally when focus leaves the wrapper.
--}}
@php($id = $id ?? 'tooltip-'.\Illuminate\Support\Str::random(8))

<span class="tooltip tooltip--{{ $placement }}">
    <button type="button" class="tooltip__trigger" aria-describedby="{{ $id }}">
        {{ $slot }}
    </button>
    <span id="{{ $id }}" class="tooltip__bubble" role="tooltip">{{ $text }}</span>
</span>
