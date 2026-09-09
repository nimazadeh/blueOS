@extends('layouts.admin')

@section('title', __('blue.admin.actions.create').' — '.__('blue.admin.nav.portfolio'))

@section('content')
    <header class="admin-page-header">
        <div>
            <h1 class="admin-section__title">{{ __('blue.admin.actions.create') }} — {{ __('blue.admin.nav.portfolio') }}</h1>
            <p class="admin-section__lead">{{ __('blue.admin.portfolio.create_lead') }}</p>
        </div>
    </header>

    @include('admin.portfolio._form', [
        'technologies' => $technologies,
        'statuses' => $statuses,
        'formAction' => route('admin.portfolio.store'),
    ])
@endsection
