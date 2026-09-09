@extends('layouts.admin')

@section('title', __('blue.admin.dashboard_title'))

@section('content')
    <header class="admin-page-header">
        <div>
            <h1 class="admin-section__title">{{ __('blue.admin.dashboard_title') }}</h1>
            <p class="admin-section__lead">{{ __('blue.admin.dashboard_lead') }}</p>
        </div>
    </header>

    <section class="stats-grid" aria-label="{{ __('blue.admin.dashboard_overview') }}">
        <a class="stat-card" href="{{ route('admin.products.index') }}">
            <span class="stat-card__value">{{ $counts['products'] }}</span>
            <span class="stat-card__label">{{ __('blue.admin.nav.products') }}</span>
        </a>
        <a class="stat-card" href="{{ route('admin.portfolio.index') }}">
            <span class="stat-card__value">{{ $counts['portfolio'] }}</span>
            <span class="stat-card__label">{{ __('blue.admin.nav.portfolio') }}</span>
        </a>
        <a class="stat-card" href="{{ route('admin.services.index') }}">
            <span class="stat-card__value">{{ $counts['services'] }}</span>
            <span class="stat-card__label">{{ __('blue.admin.nav.services') }}</span>
        </a>
        <a class="stat-card" href="{{ route('admin.leads.index') }}">
            <span class="stat-card__value">{{ $counts['new_leads'] }}</span>
            <span class="stat-card__label">{{ __('blue.admin.dashboard_new_leads') }}</span>
        </a>
    </section>

    <section class="admin-panel" aria-labelledby="recent-activity-title">
        <div class="admin-panel__header">
            <h2 id="recent-activity-title" class="text-h3">{{ __('blue.admin.dashboard_recent_activity') }}</h2>
            <a href="{{ route('admin.activity') }}" class="text-small">{{ __('blue.admin.actions.view_all') }}</a>
        </div>

        @if($recentActivity->isEmpty())
            <x-ui.empty-state
                :title="__('blue.admin.dashboard_no_activity')"
                :description="__('blue.admin.dashboard_no_activity_hint')"
            />
        @else
            <ul class="admin-list" role="list">
                @foreach($recentActivity as $entry)
                    <li class="admin-list__row">
                        <span class="admin-list__action">{{ $entry->action }}</span>
                        <span class="admin-list__meta">
                            {{ $entry->actor?->name ?? __('blue.admin.system') }}
                            — {{ $entry->created_at?->diffForHumans() }}
                        </span>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    <p class="admin-note">{{ __('blue.admin.dashboard_note', ['total' => $totalLeads]) }}</p>
@endsection
