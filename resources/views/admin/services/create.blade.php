@extends('layouts.admin')

@section('title', __('blue.admin.actions.create').' — '.__('blue.admin.nav.services'))

@section('content')
    <header class="admin-page-header">
        <div>
            <h1 class="admin-section__title">{{ __('blue.admin.actions.create') }} — {{ __('blue.admin.nav.services') }}</h1>
            <p class="admin-section__lead">{{ __('blue.admin.services.create_lead') }}</p>
        </div>
    </header>

    @include('admin.services._form', [
        'statuses' => $statuses,
        'formAction' => route('admin.services.store'),
    ])
@endsection
