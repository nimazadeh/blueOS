@extends('layouts.public.app', ['meta' => $meta])

@section('content')
    <article class="page-detail">
        <header class="page-detail__header">
            <div class="container">
                <p class="eyebrow">{{ __('blue.products.category') }}</p>
                <h1 class="text-display">{{ $product->title }}</h1>
                @if($product->excerpt)
                    <p class="text-body-large page-detail__lead">{{ $product->excerpt }}</p>
                @endif
            </div>
        </header>

        <div class="container page-detail__layout">
            <div class="page-detail__main">
                @if($product->coverUrl())
                    <figure class="page-detail__figure">
                        <img src="{{ route('media.show', $product->cover()) }}"
                             alt="{{ $product->cover()->alt_text ?? $product->title }}"
                             loading="lazy" decoding="async">
                    </figure>
                @endif

                @if($product->description)
                    <div class="prose">
                        <p>{{ $product->description }}</p>
                    </div>
                @endif

                @if($product->features->isNotEmpty())
                    <h2 class="text-h2">{{ __('blue.products.features') }}</h2>
                    <ul class="feature-list" role="list">
                        @foreach($product->features as $feature)
                            <li class="feature-list__item">
                                <strong class="feature-list__title">{{ $feature->title }}</strong>
                                @if($feature->description)
                                    <span class="feature-list__description">{{ $feature->description }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <aside class="page-detail__aside">
                <h2 class="text-h3">{{ __('blue.products.build_with') }}</h2>
                @if($product->technologies->isNotEmpty())
                    <ul class="chip-list" role="list">
                        @foreach($product->technologies as $technology)
                            <li class="chip"><x-ui.badge>{{ $technology->name }}</x-ui.badge></li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-small">{{ __('blue.products.no_technologies') }}</p>
                @endif

                @php($gallery = $product->galleryMedia())
                @if($gallery->isNotEmpty())
                    <h2 class="text-h3">{{ __('blue.products.gallery') }}</h2>
                    <div class="gallery-grid">
                        @foreach($gallery as $image)
                            <img src="{{ route('media.show', $image) }}"
                                 alt="{{ $image->alt_text ?? $product->title }}" loading="lazy" decoding="async">
                        @endforeach
                    </div>
                @endif

                <div class="page-detail__cta">
                    <x-ui.button href="{{ route('home').'#start-project' }}" variant="primary" size="lg">
                        {{ __('blue.home.cta_start') }}
                    </x-ui.button>
                </div>
            </aside>
        </div>
    </article>
@endsection
