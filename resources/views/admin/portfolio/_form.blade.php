@props([
    'project' => null,
    'technologies' => collect(),
    'statuses' => [],
    'formAction' => null,
    'method' => 'POST',
])

<form method="POST" action="{{ $formAction }}" enctype="multipart/form-data">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="admin-form-grid">
        <div class="field">
            <label class="field__label" for="title">{{ __('blue.admin.labels.title') }}</label>
            <input class="field__control" id="title" name="title" type="text" required maxlength="200"
                   value="{{ old('title', $project?->title) }}">
            @error('title') <p class="field__error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label class="field__label" for="slug">{{ __('blue.admin.labels.slug') }}</label>
            <input class="field__control" id="slug" name="slug" type="text" maxlength="191"
                   placeholder="{{ __('blue.admin.hints.slug') }}" value="{{ old('slug', $project?->slug) }}">
            @error('slug') <p class="field__error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label class="field__label" for="summary">{{ __('blue.admin.labels.summary') }}</label>
            <textarea class="field__control" id="summary" name="summary" rows="3" maxlength="500">{{ old('summary', $project?->summary) }}</textarea>
            @error('summary') <p class="field__error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label class="field__label" for="challenge">{{ __('blue.admin.labels.challenge') }}</label>
            <textarea class="field__control" id="challenge" name="challenge" rows="6" maxlength="20000">{{ old('challenge', $project?->challenge) }}</textarea>
            <p class="field__hint">{{ __('blue.admin.hints.case_study') }}</p>
            @error('challenge') <p class="field__error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label class="field__label" for="solution">{{ __('blue.admin.labels.solution') }}</label>
            <textarea class="field__control" id="solution" name="solution" rows="6" maxlength="20000">{{ old('solution', $project?->solution) }}</textarea>
            @error('solution') <p class="field__error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label class="field__label" for="results">{{ __('blue.admin.labels.results') }}</label>
            <textarea class="field__control" id="results" name="results" rows="6" maxlength="20000">{{ old('results', $project?->results) }}</textarea>
            <p class="field__hint">{{ __('blue.admin.hints.results') }}</p>
            @error('results') <p class="field__error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label class="field__label" for="status">{{ __('blue.admin.labels.status') }}</label>
            <select class="field__control" id="status" name="status" required>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" @selected(old('status', $project?->status ?? 'draft') === $status)>
                        {{ __('blue.admin.statuses.'.$status) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="field field--inline">
            <input id="featured" name="featured" type="checkbox" value="1"
                   @checked(old('featured', $project?->featured ?? false))>
            <label for="featured">{{ __('blue.admin.labels.featured') }}</label>
        </div>

        <div class="field">
            <label class="field__label" for="published_at">{{ __('blue.admin.labels.published_at') }}</label>
            <input class="field__control" id="published_at" name="published_at" type="date"
                   value="{{ old('published_at', $project?->published_at?->format('Y-m-d')) }}">
        </div>
    </div>

    <fieldset class="admin-fieldset">
        <legend class="admin-fieldset__legend">{{ __('blue.admin.labels.technologies') }}</legend>
        <div class="admin-checkbox-grid">
            @foreach($technologies as $technology)
                <div class="field field--inline">
                    <input id="technology-{{ $technology->id }}" name="technology_ids[]" type="checkbox" value="{{ $technology->id }}"
                           @checked(in_array($technology->id, old('technology_ids', $project?->technologies->pluck('id')->all() ?? []), true))>
                    <label for="technology-{{ $technology->id }}">{{ $technology->name }}</label>
                </div>
            @endforeach
        </div>
        @error('technology_ids') <p class="field__error">{{ $message }}</p> @enderror
    </fieldset>

    <fieldset class="admin-fieldset">
        <legend class="admin-fieldset__legend">{{ __('blue.admin.labels.media') }}</legend>
        <div class="field">
            <label class="field__label" for="cover">{{ __('blue.admin.labels.cover') }}</label>
            <input class="field__control" id="cover" name="cover" type="file" accept="image/jpeg,image/png,image/webp,image/avif">
            @error('cover') <p class="field__error">{{ $message }}</p> @enderror
        </div>
        <div class="field">
            <label class="field__label" for="gallery">{{ __('blue.admin.labels.gallery') }}</label>
            <input class="field__control" id="gallery" name="gallery[]" type="file" accept="image/jpeg,image/png,image/webp,image/gif" multiple>
            @error('gallery') <p class="field__error">{{ $message }}</p> @enderror
        </div>

        @if($project && $project->relationLoaded('media') && $project->media->isNotEmpty())
            <div class="admin-media-preview">
                @foreach($project->media as $file)
                    <figure class="admin-media-thumb">
                        <img src="{{ route('media.show', $file) }}" alt="{{ $file->alt_text ?? '' }}" loading="lazy">
                        <figcaption class="admin-media-thumb__meta">
                            <span>{{ $file->collection }} · {{ $file->filename }}</span>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        @endif
    </fieldset>

    <div class="admin-form-actions">
        <x-ui.button type="submit">{{ __('blue.admin.actions.save') }}</x-ui.button>
        <x-ui.button :href="route('admin.portfolio.index')" variant="ghost">{{ __('blue.admin.actions.cancel') }}</x-ui.button>
    </div>
</form>
