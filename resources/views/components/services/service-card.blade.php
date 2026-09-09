@props([
    // Presentation contract: a published App\Domains\Services\Models\Service.
    'service' => null,
])

@if ($service)
    <article class="service-card" data-reveal>
        <span class="service-card__icon" aria-hidden="true">{{ $service->icon ?? '◆' }}</span>
        <h3 class="service-card__title">{{ $service->title }}</h3>
        <p class="service-card__description">{{ $service->short_description }}</p>
        <p class="service-card__more">
            <a class="link" href="{{ route('services.show', $service->slug) }}">{{ __('blue.services.learn_more') }}</a>
        </p>
    </article>
@endif
