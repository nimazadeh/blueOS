@extends('layouts.admin')

@section('title', __('blue.admin.nav.products'))

@section('content')
    <header class="admin-page-header">
        <div>
            <h1 class="admin-section__title">{{ __('blue.admin.nav.products') }}</h1>
            <p class="admin-section__lead">{{ __('blue.admin.products.lead') }}</p>
        </div>
        <x-ui.button :href="route('admin.products.create')">{{ __('blue.admin.actions.create') }}</x-ui.button>
    </header>

    <form method="GET" action="{{ route('admin.products.index') }}" class="admin-filters">
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

    @if($products->isEmpty())
        <x-ui.empty-state
            :title="__('blue.admin.products.empty_title')"
            :description="__('blue.admin.products.empty_description')"
        />
    @else
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th scope="col">{{ __('blue.admin.labels.title') }}</th>
                        <th scope="col">{{ __('blue.admin.labels.status') }}</th>
                        <th scope="col">{{ __('blue.admin.labels.type') }}</th>
                        <th scope="col">{{ __('blue.admin.labels.featured') }}</th>
                        <th scope="col" class="admin-table__actions-col">{{ __('blue.admin.labels.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                        <tr>
                            <td>
                                <strong>{{ $product->title }}</strong>
                                <span class="admin-table__sub">{{ $product->slug }}</span>
                            </td>
                            <td>
                                <x-ui.badge :tone="$product->isPublished() ? 'success' : 'accent'">
                                    {{ __('blue.admin.statuses.'.$product->status) }}
                                </x-ui.badge>
                            </td>
                            <td>{{ __('blue.admin.types.'.$product->type) }}</td>
                            <td>{{ $product->featured ? __('blue.admin.labels.yes') : __('blue.admin.labels.no') }}</td>
                            <td class="admin-table__actions-col">
                                <div class="admin-actions">
                                    <x-ui.button :href="route('admin.products.edit', $product)" variant="secondary" size="sm">
                                        {{ __('blue.admin.actions.edit') }}
                                    </x-ui.button>
                                    @if($product->isPublished())
                                        <form method="POST" action="{{ route('admin.products.unpublish', $product) }}">
                                            @csrf
                                            <button type="submit" class="button button--secondary button--sm">{{ __('blue.admin.actions.unpublish') }}</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.products.publish', $product) }}">
                                            @csrf
                                            <button type="submit" class="button button--secondary button--sm">{{ __('blue.admin.actions.publish') }}</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
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

        {{ $products->links('vendor.pagination.blue') }}
    @endif
@endsection
