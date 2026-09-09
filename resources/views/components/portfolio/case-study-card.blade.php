@props([
    // Presentation contract for a portfolio case study card. Phase 1B: empty
    // state only — no invented clients or results (brief rule).
    'project' => null,
])

@if ($project)
    <article class="card" data-reveal>
        <div class="card__media">
            @if($project['cover'])
                <img src="{{ $project['cover'] }}" alt="{{ $project['title'] }}" loading="lazy" decoding="async">
            @endif
        </div>
        <div class="card__body">
            @if($project['client'])
                <x-ui.badge>{{ $project['client'] }}</x-ui.badge>
            @endif
            <h3 class="text-h3">{{ $project['title'] }}</h3>
            <p class="text-small">{{ $project['summary'] ?? '' }}</p>
        </div>
    </article>
@endif
