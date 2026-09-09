@extends('layouts.admin')

@section('title', __('blue.admin.actions.edit').' — '.$service->title)

@section('content')
    <header class="admin-page-header">
        <div>
            <h1 class="admin-section__title">{{ $service->title }}</h1>
            <p class="admin-section__lead">{{ $service->slug }}</p>
        </div>
        @if($service->isPublished())
            <x-ui.button :href="route('services.show', $service->slug)" variant="secondary">
                {{ __('blue.admin.actions.view_public') }}
            </x-ui.button>
        @endif
    </header>

    @include('admin.services._form', [
        'service' => $service,
        'statuses' => $statuses,
        'formAction' => route('admin.services.update', $service),
        'method' => 'PUT',
    ])
@endsection
