@extends('layouts.admin')

@section('title', __('blue.admin.actions.edit').' — '.$project->title)

@section('content')
    <header class="admin-page-header">
        <div>
            <h1 class="admin-section__title">{{ $project->title }}</h1>
            <p class="admin-section__lead">{{ $project->slug }}</p>
        </div>
        @if($project->isPublished())
            <x-ui.button :href="route('portfolio.show', $project->slug)" variant="secondary">
                {{ __('blue.admin.actions.view_public') }}
            </x-ui.button>
        @endif
    </header>

    @include('admin.portfolio._form', [
        'project' => $project,
        'technologies' => $technologies,
        'statuses' => $statuses,
        'formAction' => route('admin.portfolio.update', $project),
        'method' => 'PUT',
    ])
@endsection
