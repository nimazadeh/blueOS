@props([
    'products' => collect(),   // Presentation data; empty list renders the empty state.
])

<section class="section" id="products" aria-labelledby="products-title">
    <div class="container">
        <x-ui.section-heading
            align="center"
            :eyebrow="__('blue.products.eyebrow')"
            :title="__('blue.products.title')"
            :description="__('blue.products.description')"
        />

        @if ($products->isEmpty())
            <x-ui.empty-state
                :title="__('blue.products.empty_title')"
                :description="__('blue.products.empty_description')"
            />
        @else
            <div class="product-grid">
                @foreach ($products as $product)
                    <x-products.product-card :product="$product" />
                @endforeach
            </div>
        @endif
    </div>
</section>
