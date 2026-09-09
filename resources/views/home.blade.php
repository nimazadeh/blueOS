@extends('layouts.public.app', ['meta' => $meta])

@section('content')
    {{-- ── Hero ──────────────────────────────────────────────────────────── --}}
    <section class="hero" aria-labelledby="hero-title">
        <div class="container">
            <div class="hero__inner">
                <p class="eyebrow hero__eyebrow" data-reveal>{{ __('blue.home.eyebrow') }}</p>
                <h1 id="hero-title" class="hero__title text-display" data-reveal>
                    {{ __('blue.home.headline') }}
                </h1>
                <p class="hero__description" data-reveal>{{ __('blue.home.lead') }}</p>
                <div class="hero__actions" data-reveal>
                    <x-ui.button href="#start-project" size="lg">
                        {{ __('blue.home.cta_start') }}
                    </x-ui.button>
                    <x-ui.button href="{{ route('products.index') }}" variant="secondary" size="lg">
                        {{ __('blue.home.cta_products') }}
                    </x-ui.button>
                </div>
            </div>

            {{-- Static, decorative visual module (3D may enhance later). --}}
            <div class="hero__visual" aria-hidden="true"></div>
        </div>
    </section>

    {{-- ── Featured products (database-driven, empty state when none) ─────── --}}
    <x-products.featured-list :products="$products" />

    {{-- ── Services (database-driven) ─────────────────────────────────────── --}}
    <x-services.featured-grid :services="$services" />

    {{-- ── Portfolio preview (database-driven) ─────────────────────────────── --}}
    <x-portfolio.preview-list :projects="$projects" />

    {{-- ── Lead-generation CTA ─────────────────────────────────────────────── --}}
    <section class="cta-section" id="start-project" aria-labelledby="cta-title">
        <div class="container">
            <div class="cta-section__panel" data-reveal>
                <p class="eyebrow">{{ __('blue.home.cta_eyebrow') }}</p>
                <h2 id="cta-title" class="cta-section__title text-h2">{{ __('blue.home.cta_title') }}</h2>
                <p class="cta-section__description">{{ __('blue.home.cta_description') }}</p>

                <x-public.lead-form
                    :project-types="['web-app', 'saas', 'integration', 'automation', 'other']"
                />
            </div>
        </div>
    </section>
@endsection
