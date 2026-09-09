<!DOCTYPE html>
<html lang="{{ \App\Support\Locale::htmlLang() }}" dir="{{ \App\Support\Locale::direction() }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO slot: MetaResolver value object + optional page head extras. --}}
    <title>{{ $meta?->title ?? config('app.name') }}</title>
    <meta name="description" content="{{ $meta?->description ?? '' }}">
    @if($meta?->canonical)
        <link rel="canonical" href="{{ $meta->canonical }}">
    @endif
    <meta name="robots" content="{{ $meta?->robots ?? 'index,follow' }}">
    <meta property="og:title" content="{{ $meta?->ogTitle ?? ($meta?->title ?? '') }}">
    <meta property="og:description" content="{{ $meta?->ogDescription ?? ($meta?->description ?? '') }}">
    <meta property="og:type" content="{{ $meta?->ogType ?? 'website' }}">
    <meta name="twitter:card" content="{{ $meta?->twitterCard ?? 'summary_large_image' }}">

    @stack('head')

    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body class="app-shell" data-locale="{{ app()->getLocale() }}" data-direction="{{ \App\Support\Locale::direction() }}">
    <a class="skip-link" href="#main">@lang('blue.skip_to_content')</a>

    <x-public.header />

    <main id="main" class="app-main">
        @yield('content')
    </main>

    <x-public.footer />
</body>
</html>
