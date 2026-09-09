@props([
    'projects' => collect(),   // Presentation data; empty list renders the empty state.
])

<section class="section" id="portfolio" aria-labelledby="portfolio-title">
    <div class="container">
        <x-ui.section-heading
            align="center"
            :eyebrow="__('blue.portfolio.eyebrow')"
            :title="__('blue.portfolio.title')"
            :description="__('blue.portfolio.description')"
        />

        @if ($projects->isEmpty())
            <x-ui.empty-state
                :title="__('blue.portfolio.empty_title')"
                :description="__('blue.portfolio.empty_description')"
            />
        @else
            <div class="portfolio-grid">
                @foreach ($projects as $project)
                    <x-portfolio.case-study-card :project="$project" />
                @endforeach
            </div>
        @endif
    </div>
</section>
