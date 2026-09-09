@extends('layouts.public.app', ['meta' => $meta])

@section('content')
    <article class="page-detail">
        <header class="page-detail__header">
            <div class="container">
                <p class="eyebrow">{{ __('blue.services.eyebrow') }}</p>
                <h1 class="text-display">{{ $service->title }}</h1>
                @if($service->short_description)
                    <p class="text-body-large page-detail__lead">{{ $service->short_description }}</p>
                @endif
            </div>
        </header>

        <div class="container page-detail__layout">
            <div class="page-detail__main">
                @if($service->description)
                    <div class="prose">
                        <p>{{ $service->description }}</p>
                    </div>
                @endif
            </div>

            <aside class="page-detail__aside">
                <div class="page-detail__cta">
                    <x-ui.button href="{{ route('home').'#start-project' }}" variant="primary" size="lg">
                        {{ __('blue.home.cta_start') }}
                    </x-ui.button>
                </div>
            </aside>
        </div>
    </article>
@endsection
