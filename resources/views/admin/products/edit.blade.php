@extends('layouts.admin')

@section('title', __('blue.admin.actions.edit').' — '.$product->title)

@section('content')
    <header class="admin-page-header">
        <div>
            <h1 class="admin-section__title">{{ $product->title }}</h1>
            <p class="admin-section__lead">{{ $product->slug }}</p>
        </div>
        @if($product->isPublished())
            <div class="admin-actions">
                <x-ui.button :href="route('products.show', $product->slug)" variant="secondary">
                    {{ __('blue.admin.actions.view_public') }}
                </x-ui.button>
            </div>
        @endif
    </header>

    @include('admin.products._form', [
        'product' => $product,
        'technologies' => $technologies,
        'statuses' => $statuses,
        'types' => $types,
        'formAction' => route('admin.products.update', $product),
        'method' => 'PUT',
    ])
@endsection
