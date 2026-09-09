@props([
    'projectTypes' => [],
])

{{-- Public lead-generation form (Phase 2). Posts to routes.leads.store. --}}
<form method="POST" action="{{ route('leads.store') }}" class="lead-form" novalidate>
    @csrf

    @if(session('lead_submitted'))
        <p class="lead-form__success" role="status">{{ session('status') }}</p>
    @endif

    @if($errors->any())
        <div class="form-error" role="alert">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="lead-form__grid">
        <div class="field">
            <label class="field__label" for="lead-name">{{ __('blue.leads.form.name') }}</label>
            <input class="field__control" id="lead-name" name="name" type="text" required
                   maxlength="120" value="{{ old('name') }}" autocomplete="name">
            @error('name') <p class="field__error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label class="field__label" for="lead-email">{{ __('blue.leads.form.email') }}</label>
            <input class="field__control" id="lead-email" name="email" type="email" required
                   maxlength="190" value="{{ old('email') }}" autocomplete="email">
            @error('email') <p class="field__error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label class="field__label" for="lead-phone">{{ __('blue.leads.form.phone') }}</label>
            <input class="field__control" id="lead-phone" name="phone" type="tel" maxlength="40"
                   value="{{ old('phone') }}" autocomplete="tel">
        </div>

        <div class="field">
            <label class="field__label" for="lead-company">{{ __('blue.leads.form.company') }}</label>
            <input class="field__control" id="lead-company" name="company" type="text"
                   maxlength="120" value="{{ old('company') }}" autocomplete="organization">
        </div>

        <div class="field">
            <label class="field__label" for="lead-type">{{ __('blue.leads.form.project_type') }}</label>
            <select class="field__control" id="lead-type" name="project_type">
                <option value="">—</option>
                @foreach($projectTypes as $type)
                    <option value="{{ $type }}" @selected(old('project_type') === $type)>{{ $type }}</option>
                @endforeach
            </select>
        </div>

        <div class="field lead-form__message">
            <label class="field__label" for="lead-message">{{ __('blue.leads.form.message') }}</label>
            <textarea class="field__control" id="lead-message" name="message" rows="5" required
                      maxlength="4000">{{ old('message') }}</textarea>
            @error('message') <p class="field__error">{{ $message }}</p> @enderror
        </div>
    </div>

    <x-ui.button type="submit" size="lg">{{ __('blue.leads.form.submit') }}</x-ui.button>
</form>
