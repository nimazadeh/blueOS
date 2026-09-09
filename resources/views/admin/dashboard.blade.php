@extends('layouts.admin')

@section('title', __('blue.admin.dashboard_title'))

@section('content')
    <section class="admin-section">
        <h1 class="admin-section__title">@lang('blue.admin.dashboard_title')</h1>
        <p class="admin-section__lead">@lang('blue.admin.dashboard_lead')</p>

        <dl class="admin-meta">
            <div class="admin-meta__row">
                <dt>@lang('blue.admin.signed_in_as')</dt>
                <dd>{{ $user->name }} &lt;{{ $user->email }}&gt;</dd>
            </div>
        </dl>

        <form method="POST" action="{{ route('admin.logout') }}" class="admin-logout">
            @csrf
            <button type="submit" class="button button--ghost">@lang('blue.admin.sign_out')</button>
        </form>
    </section>
@endsection
