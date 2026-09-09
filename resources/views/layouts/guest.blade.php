<!DOCTYPE html>
<html lang="{{ \App\Support\Locale::htmlLang() }}" dir="{{ \App\Support\Locale::direction() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex,nofollow">

    <title>@yield('title', __('blue.admin.control')) — {{ config('app.name') }}</title>

    @vite(['resources/scss/admin.scss', 'resources/js/admin.js'])
</head>
<body class="guest-shell" data-locale="{{ app()->getLocale() }}" data-direction="{{ \App\Support\Locale::direction() }}">
    <main class="guest-main">
        @yield('content')
    </main>
</body>
</html>
