@extends('layouts.public.app', ['meta' => $meta])

@section('content')
    <article class="page-detail">
        <header class="page-detail__header page-detail__header--case">
            <div class="container">
                <p class="eyebrow">{{ __('blue.portfolio.case_study') }}</p>
                <h1 class="text-display">{{ $project->title }}</h1>
                @if($project->summary)
                    <p class="text-body-large page-detail__lead">{{ $project->summary }}</p>
                @endif
            </div>
        </header>

        <div class="container page-detail__layout">
            <div class="page-detail__main">
                @if($project->coverUrl())
                    <figure class="page-detail__figure">
                        <img src="{{ route('media.show', $project->cover()) }}"
                             alt="{{ $project->cover()->alt_text ?? $project->title }}"
                             loading="lazy" decoding="async">
                    </figure>
                @endif

                <section class="case-section" aria-labelledby="case-challenge">
                    <h2 id="case-challenge" class="text-h2">{{ __('blue.portfolio.challenge') }}</h2>
                    <p>{{ $project->challenge ?? __('blue.portfolio.challenge_placeholder') }}</p>
                </section>

                <section class="case-section" aria-labelledby="case-solution">
                    <h2 id="case-solution" class="text-h2">{{ __('blue.portfolio.solution') }}</h2>
                    <p>{{ $project->solution ?? __('blue.portfolio.solution_placeholder') }}</p>
                </section>

                <section class="case-section" aria-labelledby="case-results">
                    <h2 id="case-results" class="text-h2">{{ __('blue.portfolio.results') }}</h2>
                    <p>{{ $project->results ?? __('blue.portfolio.results_placeholder') }}</p>
                </section>

                @php($gallery = $project->galleryMedia())
                @if($gallery->isNotEmpty())
                    <h2 class="text-h2">{{ __('blue.portfolio.gallery') }}</h2>
                    <div class="gallery-grid">
                        @foreach($gallery as $image)
                            <img src="{{ route('media.show', $image) }}"
                                 alt="{{ $image->alt_text ?? $project->title }}" loading="lazy" decoding="async">
                        @endforeach
                    </div>
                @endif
            </div>

            <aside class="page-detail__aside">
                <h2 class="text-h3">{{ __('blue.products.build_with') }}</h2>
                @if($project->technologies->isNotEmpty())
                    <ul class="chip-list" role="list">
                        @foreach($project->technologies as $technology)
                            <li class="chip"><x-ui.badge>{{ $technology->name }}</x-ui.badge></li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-small">{{ __('blue.products.no_technologies') }}</p>
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
