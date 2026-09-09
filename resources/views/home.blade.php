@extends('layouts.public.app', ['meta' => $meta])

@section('head')
    {{-- Page-specific head extras (e.g. preloads) land here. --}}
@endsection

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
                    <x-ui.button href="#products" variant="secondary" size="lg">
                        {{ __('blue.home.cta_products') }}
                    </x-ui.button>
                </div>
            </div>

            {{-- Static, decorative visual module (3D may enhance later). --}}
            <div class="hero__visual" aria-hidden="true"></div>
        </div>
    </section>

    {{-- ── Featured products (empty state, no fabricated content) ────────── --}}
    <x-products.featured-list :products="collect()" />

    {{-- ── Services (real offering catalog) ──────────────────────────────── --}}
    <x-services.featured-grid :services="$services" />

    {{-- ── Portfolio preview (empty state) ───────────────────────────────── --}}
    <x-portfolio.preview-list :projects="collect()" />

    {{-- ── Lead-generation CTA ───────────────────────────────────────────── --}}
    <section class="cta-section" id="start-project" aria-labelledby="cta-title">
        <div class="container">
            <div class="cta-section__panel" data-reveal>
                <p class="eyebrow">{{ __('blue.home.cta_eyebrow') }}</p>
                <h2 id="cta-title" class="cta-section__title text-h2">{{ __('blue.home.cta_title') }}</h2>
                <p class="cta-section__description">{{ __('blue.home.cta_description') }}</p>
                <x-ui.badge tone="warning" :dot="true">{{ __('blue.home.cta_status') }}</x-ui.badge>
            </div>
        </div>
    </section>
@endsection
