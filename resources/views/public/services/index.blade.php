@extends('layouts.public.app', ['meta' => $meta])

@section('content')
    <section class="section section--first" aria-labelledby="services-title">
        <div class="container">
            <x-ui.section-heading
                align="center"
                :eyebrow="__('blue.services.eyebrow')"
                :title="__('blue.services.title')"
                :description="__('blue.services.description')"
            />

            @if($services->isEmpty())
                <x-ui.empty-state
                    :title="__('blue.services.empty_title')"
                    :description="__('blue.services.empty_description')"
                />
            @else
                <div class="service-grid">
                    @foreach($services as $service)
                        <x-services.service-card :service="$service" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
