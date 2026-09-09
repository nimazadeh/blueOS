@props([
    'icon' => '◆',
    'title' => null,
    'description' => null,
])

<article class="service-card" data-reveal>
    <span class="service-card__icon" aria-hidden="true">{{ $icon }}</span>
    <h3 class="service-card__title">{{ $title }}</h3>
    <p class="service-card__description">{{ $description }}</p>
</article>
