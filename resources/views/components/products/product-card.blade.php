@props([
    // Presentation contract: a published App\Domains\Products\Models\Product.
    'product' => null,
])

@if ($product)
    <article class="card" data-reveal>
        @if($product->coverUrl())
            <div class="card__media">
                <img src="{{ route('media.show', $product->cover()) }}"
                     alt="{{ $product->cover()->alt_text ?? $product->title }}"
                     loading="lazy" decoding="async">
            </div>
        @endif
        <div class="card__body">
            <div class="card__meta">
                <x-ui.badge tone="accent">{{ __('blue.admin.types.'.$product->type) }}</x-ui.badge>
            </div>
            <h3 class="text-h3">{{ $product->title }}</h3>
            <p class="text-small">{{ $product->excerpt }}</p>
        </div>
        <div class="card__footer">
            <x-ui.button :href="route('products.show', $product->slug)" variant="ghost" size="sm">
                {{ __('blue.products.view') }}
            </x-ui.button>
        </div>
    </article>
@endif
