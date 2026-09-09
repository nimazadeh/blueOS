@props([
    // Presentation contract for a product card. Phase 1B: renders the empty
    // state (no fake products). Phase 2+ passes real Product models.
    'product' => null,
])

@if ($product)
    <article class="card" data-reveal>
        <div class="card__media">
            @if($product['cover'])
                <img src="{{ $product['cover'] }}" alt="{{ $product['name'] }}" loading="lazy" decoding="async">
            @endif
        </div>
        <div class="card__body">
            <div class="card__meta">
                <x-ui.badge tone="accent">{{ $product['category'] ?? __('blue.products.category') }}</x-ui.badge>
            </div>
            <h3 class="text-h3">{{ $product['name'] }}</h3>
            <p class="text-small">{{ $product['subtitle'] ?? '' }}</p>
        </div>
        <div class="card__footer">
            <x-ui.button href="#" variant="ghost" size="sm">{{ __('blue.products.view') }}</x-ui.button>
        </div>
    </article>
@endif
