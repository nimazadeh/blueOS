<!DOCTYPE html>
<html lang="{{ \App\Support\Locale::htmlLang() }}" dir="{{ \App\Support\Locale::direction() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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

    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body class="app-shell" data-locale="{{ app()->getLocale() }}" data-direction="{{ \App\Support\Locale::direction() }}">
    <a class="skip-link" href="#main">@lang('blue.skip_to_content')</a>

    <header class="site-header">
        <div class="site-header__inner">
            <a class="site-brand" href="{{ route('home') }}">{{ config('app.name') }}</a>
            <nav class="site-nav" aria-label="@lang('blue.primary_navigation')">
                <span class="site-nav__note">@lang('blue.foundation_nav_note')</span>
            </nav>
        </div>
    </header>

    <main id="main" class="app-main">
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="site-footer__inner">
            <span>&copy; {{ date('Y') }} {{ config('app.name') }}</span>
            <form method="POST" action="{{ route('locale.switch', ['locale' => app()->getLocale() === 'en' ? 'fa' : 'en']) }}"
                  class="locale-switcher" data-locale-switcher>
                @csrf
                <button type="submit" class="locale-switcher__button">
                    {{ app()->getLocale() === 'en' ? 'فارسی' : 'English' }}
                </button>
            </form>
        </div>
    </footer>
</body>
</html>
