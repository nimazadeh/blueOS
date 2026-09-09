@extends('layouts.guest')

@section('title', __('blue.admin.login_title'))

@section('content')
    <div class="auth-card">
        <h1 class="auth-card__title">@lang('blue.admin.login_title')</h1>
        <p class="auth-card__subtitle">@lang('blue.admin.login_subtitle')</p>

        @if ($errors->any())
            <div class="form-error" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}" class="auth-form">
            @csrf

            <div class="field">
                <label class="field__label" for="email">@lang('blue.admin.email')</label>
                <input class="field__input" id="email" type="email" name="email"
                       value="{{ old('email') }}" required autocomplete="email" autofocus
                       dir="ltr">
            </div>

            <div class="field">
                <label class="field__label" for="password">@lang('blue.admin.password')</label>
                <input class="field__input" id="password" type="password" name="password"
                       required autocomplete="current-password" dir="ltr">
            </div>

            <label class="field field--inline">
                <input type="checkbox" name="remember" value="1">
                <span>@lang('blue.admin.remember_me')</span>
            </label>

            <button type="submit" class="button button--primary button--block">
                @lang('blue.admin.sign_in')
            </button>
        </form>
    </div>
@endsection
