<!DOCTYPE html>
<html lang="{{ \App\Support\Locale::htmlLang() }}" dir="{{ \App\Support\Locale::direction() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>404 — {{ config('app.name') }}</title>
    @vite(['resources/scss/app.scss'])
</head>
<body class="app-shell">
    <main class="error-page">
        <p class="eyebrow">404</p>
        <h1 class="error-page__title">@lang('blue.error.404_title')</h1>
        <p class="error-page__lead">@lang('blue.error.404_lead')</p>
        <a class="button button--primary" href="{{ url('/') }}">@lang('blue.error.back_home')</a>
    </main>
</body>
</html>
