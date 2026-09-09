@props([
    'service' => null,
    'statuses' => [],
    'formAction' => null,
    'method' => 'POST',
])

<form method="POST" action="{{ $formAction }}">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="admin-form-grid">
        <div class="field">
            <label class="field__label" for="title">{{ __('blue.admin.labels.title') }}</label>
            <input class="field__control" id="title" name="title" type="text" required maxlength="200"
                   value="{{ old('title', $service?->title) }}">
            @error('title') <p class="field__error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label class="field__label" for="slug">{{ __('blue.admin.labels.slug') }}</label>
            <input class="field__control" id="slug" name="slug" type="text" maxlength="191"
                   placeholder="{{ __('blue.admin.hints.slug') }}" value="{{ old('slug', $service?->slug) }}">
            @error('slug') <p class="field__error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label class="field__label" for="short_description">{{ __('blue.admin.labels.short_description') }}</label>
            <textarea class="field__control" id="short_description" name="short_description" rows="3" maxlength="300">{{ old('short_description', $service?->short_description) }}</textarea>
            @error('short_description') <p class="field__error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label class="field__label" for="description">{{ __('blue.admin.labels.description') }}</label>
            <textarea class="field__control" id="description" name="description" rows="8" maxlength="10000">{{ old('description', $service?->description) }}</textarea>
            @error('description') <p class="field__error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label class="field__label" for="icon">{{ __('blue.admin.labels.icon') }}</label>
            <input class="field__control" id="icon" name="icon" type="text" maxlength="100"
                   placeholder="◆" value="{{ old('icon', $service?->icon) }}">
            <p class="field__hint">{{ __('blue.admin.hints.icon') }}</p>
        </div>

        <div class="field">
            <label class="field__label" for="status">{{ __('blue.admin.labels.status') }}</label>
            <select class="field__control" id="status" name="status" required>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" @selected(old('status', $service?->status ?? 'draft') === $status)>
                        {{ __('blue.admin.statuses.'.$status) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label class="field__label" for="sort_order">{{ __('blue.admin.labels.order') }}</label>
            <input class="field__control" id="sort_order" name="sort_order" type="number" min="0" max="999"
                   value="{{ old('sort_order', $service?->sort_order ?? 0) }}">
        </div>
    </div>

    <div class="admin-form-actions">
        <x-ui.button type="submit">{{ __('blue.admin.actions.save') }}</x-ui.button>
        <x-ui.button :href="route('admin.services.index')" variant="ghost">{{ __('blue.admin.actions.cancel') }}</x-ui.button>
    </div>
</form>
