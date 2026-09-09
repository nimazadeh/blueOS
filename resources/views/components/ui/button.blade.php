@props([
    'variant' => 'primary',   // primary | secondary | ghost | danger
    'size' => null,            // sm | lg
    'href' => null,
    'type' => 'button',
    'loading' => false,
    'disabled' => false,
    'block' => false,
])

@php
    $classes = ['button', 'button--'.$variant];

    if ($size) {
        $classes[] = 'button--'.$size;
    }
    if ($block) {
        $classes[] = 'button--block';
    }
    if ($loading) {
        $classes[] = 'button--loading';
    }
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => implode(' ', $classes)]) }}>
        {{ $slot }}
    </a>
@else
    <button
        type="{{ $type }}"
        @disabled($disabled || $loading)
        @if($loading) aria-busy="true" @endif
        {{ $attributes->merge(['class' => implode(' ', $classes)]) }}
    >
        @if($loading)
            <span class="button__spinner" aria-hidden="true"></span>
        @endif
        {{ $slot }}
    </button>
@endif
