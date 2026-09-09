@props([
    'product' => null,
    'technologies' => collect(),
    'statuses' => [],
    'types' => [],
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
                   value="{{ old('title', $product?->title) }}">
            @error('title') <p class="field__error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label class="field__label" for="slug">{{ __('blue.admin.labels.slug') }}</label>
            <input class="field__control" id="slug" name="slug" type="text" maxlength="191"
                   placeholder="{{ __('blue.admin.hints.slug') }}" value="{{ old('slug', $product?->slug) }}">
            @error('slug') <p class="field__error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label class="field__label" for="excerpt">{{ __('blue.admin.labels.excerpt') }}</label>
            <textarea class="field__control" id="excerpt" name="excerpt" rows="3" maxlength="500">{{ old('excerpt', $product?->excerpt) }}</textarea>
            @error('excerpt') <p class="field__error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label class="field__label" for="description">{{ __('blue.admin.labels.description') }}</label>
            <textarea class="field__control" id="description" name="description" rows="8" maxlength="10000">{{ old('description', $product?->description) }}</textarea>
            @error('description') <p class="field__error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label class="field__label" for="status">{{ __('blue.admin.labels.status') }}</label>
            <select class="field__control" id="status" name="status" required>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" @selected(old('status', $product?->status ?? 'draft') === $status)>
                        {{ __('blue.admin.statuses.'.$status) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label class="field__label" for="type">{{ __('blue.admin.labels.type') }}</label>
            <select class="field__control" id="type" name="type" required>
                @foreach($types as $type)
                    <option value="{{ $type }}" @selected(old('type', $product?->type ?? 'software') === $type)>
                        {{ __('blue.admin.types.'.$type) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="field field--inline">
            <input id="featured" name="featured" type="checkbox" value="1"
                   @checked(old('featured', $product?->featured ?? false))>
            <label for="featured">{{ __('blue.admin.labels.featured') }}</label>
        </div>

        <div class="field">
            <label class="field__label" for="published_at">{{ __('blue.admin.labels.published_at') }}</label>
            <input class="field__control" id="published_at" name="published_at" type="date"
                   value="{{ old('published_at', $product?->published_at?->format('Y-m-d')) }}">
            <p class="field__hint">{{ __('blue.admin.hints.published_at') }}</p>
        </div>
    </div>

    <fieldset class="admin-fieldset">
        <legend class="admin-fieldset__legend">{{ __('blue.admin.labels.features') }}</legend>
        <p class="field__hint">{{ __('blue.admin.hints.features') }}</p>
        @php($features = old('features', $product?->features->map(fn ($f) => [
            'title' => $f->title,
            'description' => $f->description,
            'sort_order' => $f->sort_order,
        ])->all() ?? []))
        <div id="product-features">
            @foreach($features as $index => $feature)
                <div class="admin-feature-row">
                    <input class="field__control" name="features[{{ $index }}][title]" type="text"
                           placeholder="{{ __('blue.admin.labels.title') }}" maxlength="200"
                           value="{{ $feature['title'] }}" aria-label="{{ __('blue.admin.labels.feature_title') }}">
                    <input class="field__control" name="features[{{ $index }}][description]" type="text"
                           placeholder="{{ __('blue.admin.labels.description') }}" maxlength="1000"
                           value="{{ $feature['description'] }}" aria-label="{{ __('blue.admin.labels.feature_description') }}">
                    <input class="field__control admin-feature-row__order" name="features[{{ $index }}][sort_order]" type="number"
                           min="0" max="999" value="{{ $feature['sort_order'] ?? $index }}"
                           aria-label="{{ __('blue.admin.labels.order') }}" title="{{ __('blue.admin.labels.order') }}">
                </div>
            @endforeach
        </div>
        <button type="button" class="button button--secondary button--sm" data-add-feature-row>
            + {{ __('blue.admin.actions.add_feature') }}
        </button>
        <template id="product-feature-row-template">
            <div class="admin-feature-row">
                <input class="field__control" name="features[INDEX][title]" type="text" placeholder="{{ __('blue.admin.labels.title') }}" maxlength="200" aria-label="{{ __('blue.admin.labels.feature_title') }}">
                <input class="field__control" name="features[INDEX][description]" type="text" placeholder="{{ __('blue.admin.labels.description') }}" maxlength="1000" aria-label="{{ __('blue.admin.labels.feature_description') }}">
                <input class="field__control admin-feature-row__order" name="features[INDEX][sort_order]" type="number" min="0" max="999" value="0" aria-label="{{ __('blue.admin.labels.order') }}" title="{{ __('blue.admin.labels.order') }}">
            </div>
        </template>
    </fieldset>

    <fieldset class="admin-fieldset">
        <legend class="admin-fieldset__legend">{{ __('blue.admin.labels.technologies') }}</legend>
        <div class="admin-checkbox-grid">
            @foreach($technologies as $technology)
                <div class="field field--inline">
                    <input id="technology-{{ $technology->id }}" name="technology_ids[]" type="checkbox" value="{{ $technology->id }}"
                           @checked(in_array($technology->id, old('technology_ids', $product?->technologies->pluck('id')->all() ?? []), true))>
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

        @if($product && $product->relationLoaded('media') && $product->media->isNotEmpty())
            <div class="admin-media-preview">
                @foreach($product->media as $file)
                    <figure class="admin-media-thumb">
                        <img src="{{ route('media.show', $file) }}" alt="{{ $file->alt_text ?? '' }}" loading="lazy">
                        <figcaption class="admin-media-thumb__meta">
                            <span>{{ $file->collection }} · {{ $file->filename }}</span>
                            <a class="text-small" href="{{ route('admin.media.index') }}">{{ __('blue.admin.actions.manage_library') }}</a>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        @endif
    </fieldset>

    <div class="admin-form-actions">
        <x-ui.button type="submit">{{ __('blue.admin.actions.save') }}</x-ui.button>
        <x-ui.button :href="route('admin.products.index')" variant="ghost">{{ __('blue.admin.actions.cancel') }}</x-ui.button>
    </div>
</form>
