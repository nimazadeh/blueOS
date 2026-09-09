@props([
    'title' => null,
    'description' => null,
    'icon' => '◌',
    'action' => null,     // route name or null
    'actionLabel' => null,
])

<div class="empty-state" data-reveal>
    <span class="empty-state__icon" aria-hidden="true">{{ $icon }}</span>

    @if($title)
        <h3 class="empty-state__title">{{ $title }}</h3>
    @endif

    @if($description)
        <p class="empty-state__description">{{ $description }}</p>
    @endif

    @if($action && $actionLabel)
        <div class="empty-state__action">
            <x-ui.button :href="route($action)" variant="secondary" size="sm">
                {{ $actionLabel }}
            </x-ui.button>
        </div>
    @endif
</div>
