@extends('layouts.admin')

@section('title', __('blue.admin.nav.services'))

@section('content')
    <header class="admin-page-header">
        <div>
            <h1 class="admin-section__title">{{ __('blue.admin.nav.services') }}</h1>
            <p class="admin-section__lead">{{ __('blue.admin.services.lead') }}</p>
        </div>
        <x-ui.button :href="route('admin.services.create')">{{ __('blue.admin.actions.create') }}</x-ui.button>
    </header>

    <form method="GET" action="{{ route('admin.services.index') }}" class="admin-filters">
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

    @if($services->isEmpty())
        <x-ui.empty-state
            :title="__('blue.admin.services.empty_title')"
            :description="__('blue.admin.services.empty_description')"
        />
    @else
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th scope="col">{{ __('blue.admin.labels.title') }}</th>
                        <th scope="col">{{ __('blue.admin.labels.status') }}</th>
                        <th scope="col">{{ __('blue.admin.labels.order') }}</th>
                        <th scope="col" class="admin-table__actions-col">{{ __('blue.admin.labels.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($services as $service)
                        <tr>
                            <td>
                                <strong>{{ $service->title }}</strong>
                                <span class="admin-table__sub">{{ $service->slug }}</span>
                            </td>
                            <td>
                                <x-ui.badge :tone="$service->isPublished() ? 'success' : 'accent'">
                                    {{ __('blue.admin.statuses.'.$service->status) }}
                                </x-ui.badge>
                            </td>
                            <td>{{ $service->sort_order }}</td>
                            <td class="admin-table__actions-col">
                                <div class="admin-actions">
                                    <x-ui.button :href="route('admin.services.edit', $service)" variant="secondary" size="sm">
                                        {{ __('blue.admin.actions.edit') }}
                                    </x-ui.button>
                                    @if($service->isPublished())
                                        <form method="POST" action="{{ route('admin.services.unpublish', $service) }}">
                                            @csrf
                                            <button type="submit" class="button button--secondary button--sm">{{ __('blue.admin.actions.unpublish') }}</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.services.publish', $service) }}">
                                            @csrf
                                            <button type="submit" class="button button--secondary button--sm">{{ __('blue.admin.actions.publish') }}</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.services.destroy', $service) }}"
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

        {{ $services->links('vendor.pagination.blue') }}
    @endif
@endsection
