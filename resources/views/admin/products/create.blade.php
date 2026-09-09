@extends('layouts.admin')

@section('title', __('blue.admin.actions.create').' — '.__('blue.admin.nav.products'))

@section('content')
    <header class="admin-page-header">
        <div>
            <h1 class="admin-section__title">{{ __('blue.admin.actions.create') }} — {{ __('blue.admin.nav.products') }}</h1>
            <p class="admin-section__lead">{{ __('blue.admin.products.create_lead') }}</p>
        </div>
    </header>

    @include('admin.products._form', [
        'technologies' => $technologies,
        'statuses' => $statuses,
        'types' => $types,
        'formAction' => route('admin.products.store'),
    ])
@endsection
