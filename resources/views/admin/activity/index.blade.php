@extends('layouts.admin')

@section('title', __('blue.admin.nav.activity'))

@section('content')
    <header class="admin-page-header">
        <div>
            <h1 class="admin-section__title">{{ __('blue.admin.nav.activity') }}</h1>
            <p class="admin-section__lead">{{ __('blue.admin.activity.lead') }}</p>
        </div>
    </header>

    @if($entries->isEmpty())
        <x-ui.empty-state
            :title="__('blue.admin.activity.empty_title')"
            :description="__('blue.admin.activity.empty_description')"
        />
    @else
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th scope="col">{{ __('blue.admin.labels.action') }}</th>
                        <th scope="col">{{ __('blue.admin.labels.actor') }}</th>
                        <th scope="col">{{ __('blue.admin.labels.subject') }}</th>
                        <th scope="col">{{ __('blue.admin.labels.when') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entries as $entry)
                        <tr>
                            <td><code class="admin-code">{{ $entry->action }}</code></td>
                            <td>{{ $entry->actor?->name ?? __('blue.admin.system') }}</td>
                            <td>
                                @if($entry->subject_type)
                                    <span class="admin-table__sub">
                                        {{ class_basename($entry->subject_type) }}#{{ $entry->subject_id }}
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $entry->created_at?->format('Y-m-d H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $entries->links('vendor.pagination.blue') }}
    @endif
@endsection
