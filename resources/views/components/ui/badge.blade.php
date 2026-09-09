@props([
    'tone' => null,   // accent | success | warning | danger
    'dot' => false,
])

@php
    $classes = ['badge'];
    if ($tone) {
        $classes[] = 'badge--'.$tone;
    }
@endphp

<span {{ $attributes->merge(['class' => implode(' ', $classes)]) }}>
    @if($dot)
        <span class="badge__dot" aria-hidden="true"></span>
    @endif
    {{ $slot }}
</span>
