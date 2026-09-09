@extends('layouts.admin')

@section('title', __('blue.admin.nav.leads'))

@section('content')
    <header class="admin-page-header">
        <div>
            <h1 class="admin-section__title">{{ __('blue.admin.nav.leads') }}</h1>
            <p class="admin-section__lead">{{ __('blue.admin.leads.lead') }}</p>
        </div>
    </header>

    <form method="GET" action="{{ route('admin.leads.index') }}" class="admin-filters">
        <div class="field">
            <label class="field__label" for="status-filter">{{ __('blue.admin.labels.status') }}</label>
            <select class="field__control" id="status-filter" name="status" data-auto-submit>
                <option value="">{{ __('blue.admin.labels.all') }}</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" @selected($activeStatus === $status)>{{ __('blue.admin.leads.statuses.'.$status) }}</option>
                @endforeach
            </select>
        </div>
    </form>

    @if($leads->isEmpty())
        <x-ui.empty-state
            :title="__('blue.admin.leads.empty_title')"
            :description="__('blue.admin.leads.empty_description')"
        />
    @else
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th scope="col">{{ __('blue.admin.labels.name') }}</th>
                        <th scope="col">{{ __('blue.admin.labels.email') }}</th>
                        <th scope="col">{{ __('blue.admin.labels.status') }}</th>
                        <th scope="col">{{ __('blue.admin.labels.received') }}</th>
                        <th scope="col" class="admin-table__actions-col">{{ __('blue.admin.labels.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leads as $lead)
                        <tr>
                            <td>
                                <strong>{{ $lead->name }}</strong>
                                @if($lead->company)
                                    <span class="admin-table__sub">{{ $lead->company }}</span>
                                @endif
                            </td>
                            <td>{{ $lead->email }}</td>
                            <td>
                                <x-ui.badge :dot="true" :tone="$lead->status === 'new' ? 'warning' : 'accent'">
                                    {{ __('blue.admin.leads.statuses.'.$lead->status) }}
                                </x-ui.badge>
                            </td>
                            <td>{{ $lead->created_at?->format('Y-m-d H:i') }}</td>
                            <td class="admin-table__actions-col">
                                <x-ui.button :href="route('admin.leads.show', $lead)" variant="secondary" size="sm">
                                    {{ __('blue.admin.actions.view') }}
                                </x-ui.button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $leads->links('vendor.pagination.blue') }}
    @endif
@endsection
