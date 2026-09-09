@props([
    // Presentation contract: a published App\Domains\Portfolio\Models\PortfolioProject.
    'project' => null,
])

@if ($project)
    <article class="card" data-reveal>
        @if($project->coverUrl())
            <div class="card__media">
                <img src="{{ route('media.show', $project->cover()) }}"
                     alt="{{ $project->cover()->alt_text ?? $project->title }}"
                     loading="lazy" decoding="async">
            </div>
        @endif
        <div class="card__body">
            <h3 class="text-h3">{{ $project->title }}</h3>
            <p class="text-small">{{ $project->summary }}</p>
        </div>
        <div class="card__footer">
            <x-ui.button :href="route('portfolio.show', $project->slug)" variant="ghost" size="sm">
                {{ __('blue.portfolio.read_case_study') }}
            </x-ui.button>
        </div>
    </article>
@endif
