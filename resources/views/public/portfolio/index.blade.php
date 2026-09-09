@extends('layouts.public.app', ['meta' => $meta])

@section('content')
    <section class="section section--first" aria-labelledby="portfolio-title">
        <div class="container">
            <x-ui.section-heading
                align="center"
                :eyebrow="__('blue.portfolio.eyebrow')"
                :title="__('blue.portfolio.title')"
                :description="__('blue.portfolio.description')"
            />

            @if($projects->isEmpty())
                <x-ui.empty-state
                    :title="__('blue.portfolio.empty_title')"
                    :description="__('blue.portfolio.empty_description')"
                />
            @else
                <div class="portfolio-grid">
                    @foreach($projects as $project)
                        <x-portfolio.case-study-card :project="$project" />
                    @endforeach
                </div>
                {{ $projects->links('vendor.pagination.blue') }}
            @endif
        </div>
    </section>
@endsection
