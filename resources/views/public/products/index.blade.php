@extends('layouts.public.app', ['meta' => $meta])

@section('content')
    <section class="section section--first" aria-labelledby="products-title">
        <div class="container">
            <x-ui.section-heading
                align="center"
                :eyebrow="__('blue.products.eyebrow')"
                :title="__('blue.products.title')"
                :description="__('blue.products.description')"
            />

            @if($products->isEmpty())
                <x-ui.empty-state
                    :title="__('blue.products.empty_title')"
                    :description="__('blue.products.empty_description')"
                />
            @else
                <div class="product-grid">
                    @foreach($products as $product)
                        <x-products.product-card :product="$product" />
                    @endforeach
                </div>
                {{ $products->links('vendor.pagination.blue') }}
            @endif
        </div>
    </section>
@endsection
