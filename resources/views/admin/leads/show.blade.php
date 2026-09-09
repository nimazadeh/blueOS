@extends('layouts.admin')

@section('title', $lead->name)

@section('content')
    <header class="admin-page-header">
        <div>
            <h1 class="admin-section__title">{{ $lead->name }}</h1>
            <p class="admin-section__lead">{{ $lead->email }} · {{ $lead->created_at?->format('Y-m-d H:i') }}</p>
        </div>
    </header>

    <div class="admin-detail-grid">
        <div class="admin-panel">
            <div class="admin-meta">
                <dl class="admin-meta__row">
                    <dt>{{ __('blue.admin.labels.phone') }}</dt>
                    <dd>{{ $lead->phone ?? '—' }}</dd>
                </dl>
                <dl class="admin-meta__row">
                    <dt>{{ __('blue.admin.labels.company') }}</dt>
                    <dd>{{ $lead->company ?? '—' }}</dd>
                </dl>
                <dl class="admin-meta__row">
                    <dt>{{ __('blue.admin.labels.project_type') }}</dt>
                    <dd>{{ $lead->project_type ?? '—' }}</dd>
                </dl>
                <dl class="admin-meta__row">
                    <dt>{{ __('blue.admin.labels.source') }}</dt>
                    <dd>{{ $lead->source }}</dd>
                </dl>
            </div>

            <h2 class="text-h3">{{ __('blue.admin.labels.message') }}</h2>
            <p class="admin-lead-message">{{ $lead->message }}</p>
        </div>

        <aside class="admin-panel">
            <h2 class="text-h3">{{ __('blue.admin.labels.status') }}</h2>
            <form method="POST" action="{{ route('admin.leads.status', $lead) }}" class="admin-inline-form">
                @csrf
                <div class="field">
                    <label class="field__label" for="status">{{ __('blue.admin.labels.status') }}</label>
                    <select class="field__control" id="status" name="status" required>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" @selected($lead->status === $status)>
                                {{ __('blue.admin.leads.statuses.'.$status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <x-ui.button type="submit" size="sm">{{ __('blue.admin.actions.save') }}</x-ui.button>
            </form>

            @if($lead->statusHistory->isNotEmpty())
                <h3 class="text-small admin-subheading">{{ __('blue.admin.leads.history') }}</h3>
                <ul class="admin-list" role="list">
                    @foreach($lead->statusHistory as $history)
                        <li class="admin-list__row">
                            <span class="admin-list__action">
                                {{ __('blue.admin.leads.statuses.'.$history->old_status) }} →
                                {{ __('blue.admin.leads.statuses.'.$history->new_status) }}
                            </span>
                            <span class="admin-list__meta">
                                {{ $history->user?->name ?? __('blue.admin.system') }} ·
                                {{ $history->created_at?->diffForHumans() }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </aside>
    </div>

    <section class="admin-panel" aria-labelledby="lead-notes-title">
        <h2 id="lead-notes-title" class="text-h3">{{ __('blue.admin.leads.notes') }}</h2>

        <form method="POST" action="{{ route('admin.leads.notes', $lead) }}" class="admin-inline-form">
            @csrf
            <div class="field">
                <label class="field__label" for="note">{{ __('blue.admin.leads.note_label') }}</label>
                <textarea class="field__control" id="note" name="note" rows="4" maxlength="2000" required></textarea>
                @error('note') <p class="field__error">{{ $message }}</p> @enderror
            </div>
            <x-ui.button type="submit" size="sm">{{ __('blue.admin.actions.add_note') }}</x-ui.button>
        </form>

        @if($lead->notes->isEmpty())
            <p class="text-small">{{ __('blue.admin.leads.no_notes') }}</p>
        @else
            <ul class="admin-list" role="list">
                @foreach($lead->notes as $note)
                    <li class="admin-list__row">
                        <span class="admin-list__action">{{ $note->note }}</span>
                        <span class="admin-list__meta">
                            {{ $note->user?->name ?? __('blue.admin.system') }} ·
                            {{ $note->created_at?->diffForHumans() }}
                        </span>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
@endsection
