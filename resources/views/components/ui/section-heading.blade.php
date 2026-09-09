@props([
    'eyebrow' => null,
    'title' => null,
    'description' => null,
    'align' => 'start',   // start | center
])

<div class="section__header {{ $align === 'center' ? 'section__header--center text-center' : '' }}">
    @if($eyebrow)
        <p class="eyebrow section__eyebrow">{{ $eyebrow }}</p>
    @endif

    @if($title)
        <h2 class="section__title text-h2" data-reveal>{{ $title }}</h2>
    @endif

    @if($description)
        <p class="section__description" data-reveal>{{ $description }}</p>
    @endif
</div>
