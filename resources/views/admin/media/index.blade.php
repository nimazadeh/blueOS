@extends('layouts.admin')

@section('title', __('blue.admin.nav.media'))

@section('content')
    <header class="admin-page-header">
        <div>
            <h1 class="admin-section__title">{{ __('blue.admin.nav.media') }}</h1>
            <p class="admin-section__lead">{{ __('blue.admin.media.lead') }}</p>
        </div>
    </header>

    <section class="admin-panel" aria-labelledby="upload-title">
        <h2 id="upload-title" class="text-h3">{{ __('blue.admin.media.upload') }}</h2>
        <form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" class="admin-inline-form">
            @csrf
            <div class="field">
                <label class="field__label" for="file">{{ __('blue.admin.media.file') }}</label>
                <input class="field__control" id="file" name="file" type="file" required
                       accept="image/jpeg,image/png,image/webp,image/avif,image/gif">
                @error('file') <p class="field__error">{{ $message }}</p> @enderror
            </div>
            <div class="field">
                <label class="field__label" for="collection">{{ __('blue.admin.media.collection') }}</label>
                <select class="field__control" id="collection" name="collection" required>
                    <option value="covers">{{ __('blue.admin.media.collection_covers') }}</option>
                    <option value="gallery">{{ __('blue.admin.media.collection_gallery') }}</option>
                </select>
            </div>
            <div class="field">
                <label class="field__label" for="alt_text">{{ __('blue.admin.media.alt_text') }}</label>
                <input class="field__control" id="alt_text" name="alt_text" type="text" maxlength="255">
            </div>
            <x-ui.button type="submit">{{ __('blue.admin.actions.upload') }}</x-ui.button>
        </form>
        <p class="field__hint">{{ __('blue.admin.media.svg_disabled') }}</p>
    </section>

    @if($media->isEmpty())
        <x-ui.empty-state
            :title="__('blue.admin.media.empty_title')"
            :description="__('blue.admin.media.empty_description')"
        />
    @else
        <div class="media-grid">
            @foreach($media as $file)
                <figure class="media-card">
                    <img class="media-card__image"
                         src="{{ route('media.show', $file) }}"
                         alt="{{ $file->alt_text ?? '' }}"
                         loading="lazy" decoding="async">
                    <figcaption class="media-card__body">
                        <span class="media-card__name">{{ $file->filename }}</span>
                        <span class="media-card__meta">
                            {{ $file->collection }} · {{ $file->mime_type }} · {{ number_format($file->size / 1024, 0) }} kB
                            @if($file->width && $file->height)
                                · {{ $file->width }}×{{ $file->height }}
                            @endif
                        </span>
                    </figcaption>
                    <form method="POST" action="{{ route('admin.media.update', $file) }}" class="media-card__form">
                        @csrf
                        @method('PATCH')
                        <input class="field__control" name="alt_text" type="text" maxlength="255"
                               value="{{ $file->alt_text }}" aria-label="{{ __('blue.admin.media.alt_text') }}">
                        <button type="submit" class="button button--secondary button--sm">{{ __('blue.admin.actions.save') }}</button>
                    </form>
                    <form method="POST" action="{{ route('admin.media.destroy', $file) }}"
                          data-confirm="{{ __('blue.admin.confirm_delete') }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="button button--danger button--sm">{{ __('blue.admin.actions.delete') }}</button>
                    </form>
                </figure>
            @endforeach
        </div>
        {{ $media->links('vendor.pagination.blue') }}
    @endif
@endsection
