@extends('layouts.admin')

@section('title', __('blue.admin.nav.portfolio'))

@section('content')
    <header class="admin-page-header">
        <div>
            <h1 class="admin-section__title">{{ __('blue.admin.nav.portfolio') }}</h1>
            <p class="admin-section__lead">{{ __('blue.admin.portfolio.lead') }}</p>
        </div>
        <x-ui.button :href="route('admin.portfolio.create')">{{ __('blue.admin.actions.create') }}</x-ui.button>
    </header>

    <form method="GET" action="{{ route('admin.portfolio.index') }}" class="admin-filters">
        <div class="field">
            <label class="field__label" for="status-filter">{{ __('blue.admin.labels.status') }}</label>
            <select class="field__control" id="status-filter" name="status" data-auto-submit>
                <option value="">{{ __('blue.admin.labels.all') }}</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" @selected($activeStatus === $status)>{{ __('blue.admin.statuses.'.$status) }}</option>
                @endforeach
            </select>
        </div>
    </form>

    @if($projects->isEmpty())
        <x-ui.empty-state
            :title="__('blue.admin.portfolio.empty_title')"
            :description="__('blue.admin.portfolio.empty_description')"
        />
    @else
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th scope="col">{{ __('blue.admin.labels.title') }}</th>
                        <th scope="col">{{ __('blue.admin.labels.status') }}</th>
                        <th scope="col">{{ __('blue.admin.labels.featured') }}</th>
                        <th scope="col" class="admin-table__actions-col">{{ __('blue.admin.labels.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($projects as $project)
                        <tr>
                            <td>
                                <strong>{{ $project->title }}</strong>
                                <span class="admin-table__sub">{{ $project->slug }}</span>
                            </td>
                            <td>
                                <x-ui.badge :tone="$project->isPublished() ? 'success' : 'accent'">
                                    {{ __('blue.admin.statuses.'.$project->status) }}
                                </x-ui.badge>
                            </td>
                            <td>{{ $project->featured ? __('blue.admin.labels.yes') : __('blue.admin.labels.no') }}</td>
                            <td class="admin-table__actions-col">
                                <div class="admin-actions">
                                    <x-ui.button :href="route('admin.portfolio.edit', $project)" variant="secondary" size="sm">
                                        {{ __('blue.admin.actions.edit') }}
                                    </x-ui.button>
                                    @if($project->isPublished())
                                        <form method="POST" action="{{ route('admin.portfolio.unpublish', $project) }}">
                                            @csrf
                                            <button type="submit" class="button button--secondary button--sm">{{ __('blue.admin.actions.unpublish') }}</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.portfolio.publish', $project) }}">
                                            @csrf
                                            <button type="submit" class="button button--secondary button--sm">{{ __('blue.admin.actions.publish') }}</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.portfolio.destroy', $project) }}"
                                          data-confirm="{{ __('blue.admin.confirm_delete') }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="button button--danger button--sm">{{ __('blue.admin.actions.delete') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $projects->links('vendor.pagination.blue') }}
    @endif
@endsection
