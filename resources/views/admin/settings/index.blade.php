@extends('layouts.admin')

@section('title', __('blue.admin.nav.settings'))

@section('content')
    <header class="admin-page-header">
        <div>
            <h1 class="admin-section__title">{{ __('blue.admin.nav.settings') }}</h1>
            <p class="admin-section__lead">{{ __('blue.admin.settings.lead') }}</p>
        </div>
    </header>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="admin-panel">
        @csrf
        @method('PATCH')

        <div class="admin-form-grid">
            <div class="field">
                <label class="field__label" for="site.name">{{ __('blue.admin.settings.site_name') }}</label>
                <input class="field__control" id="site.name" name="site.name" type="text" required maxlength="120"
                       value="{{ old('site.name', $values['site.name']) }}">
                @error('site.name') <p class="field__error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label class="field__label" for="site.description">{{ __('blue.admin.settings.site_description') }}</label>
                <textarea class="field__control" id="site.description" name="site.description" rows="3" maxlength="500">{{ old('site.description', $values['site.description']) }}</textarea>
                @error('site.description') <p class="field__error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label class="field__label" for="contact.email">{{ __('blue.admin.settings.contact_email') }}</label>
                <input class="field__control" id="contact.email" name="contact.email" type="email" maxlength="190"
                       value="{{ old('contact.email', $values['contact.email']) }}">
                @error('contact.email') <p class="field__error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label class="field__label" for="contact.phone">{{ __('blue.admin.settings.contact_phone') }}</label>
                <input class="field__control" id="contact.phone" name="contact.phone" type="text" maxlength="40"
                       value="{{ old('contact.phone', $values['contact.phone']) }}">
                @error('contact.phone') <p class="field__error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label class="field__label" for="seo.default_description">{{ __('blue.admin.settings.seo_description') }}</label>
                <textarea class="field__control" id="seo.default_description" name="seo.default_description" rows="3" maxlength="500">{{ old('seo.default_description', $values['seo.default_description']) }}</textarea>
                @error('seo.default_description') <p class="field__error">{{ $message }}</p> @enderror
            </div>
        </div>

        <p class="field__hint">{{ __('blue.admin.settings.no_secrets') }}</p>

        <div class="admin-form-actions">
            <x-ui.button type="submit">{{ __('blue.admin.actions.save') }}</x-ui.button>
        </div>
    </form>
@endsection
