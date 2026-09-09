@php
    $routeName = (string) request()->route()?->getName();
    $section = explode('.', $routeName)[1] ?? 'dashboard';

    $nav = [
        ['key' => 'dashboard', 'label' => __('blue.admin.nav.dashboard'), 'route' => 'admin.dashboard'],
        ['key' => 'products', 'label' => __('blue.admin.nav.products'), 'route' => 'admin.products.index'],
        ['key' => 'portfolio', 'label' => __('blue.admin.nav.portfolio'), 'route' => 'admin.portfolio.index'],
        ['key' => 'services', 'label' => __('blue.admin.nav.services'), 'route' => 'admin.services.index'],
        ['key' => 'leads', 'label' => __('blue.admin.nav.leads'), 'route' => 'admin.leads.index'],
        ['key' => 'media', 'label' => __('blue.admin.nav.media'), 'route' => 'admin.media.index'],
        ['key' => 'settings', 'label' => __('blue.admin.nav.settings'), 'route' => 'admin.settings'],
        ['key' => 'activity', 'label' => __('blue.admin.nav.activity'), 'route' => 'admin.activity'],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ \App\Support\Locale::htmlLang() }}" dir="{{ \App\Support\Locale::direction() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex,nofollow">

    <title>@yield('title', __('blue.admin.title')) — {{ config('app.name') }}</title>

    @vite(['resources/scss/admin.scss', 'resources/js/admin.js'])
    @stack('head')
</head>
<body class="admin-shell" data-locale="{{ app()->getLocale() }}" data-direction="{{ \App\Support\Locale::direction() }}">
    <div class="admin-shell__grid">
        <aside class="admin-sidebar">
            <a class="admin-brand" href="{{ route('admin.dashboard') }}">
                {{ config('app.name') }} <span class="admin-brand__tag">@lang('blue.admin.control')</span>
            </a>

            <nav class="admin-nav" aria-label="{{ __('blue.admin.nav.label') }}">
                <ul class="admin-nav__list" role="list">
                    @foreach($nav as $item)
                        <li>
                            <a class="admin-nav__link {{ $section === $item['key'] ? 'admin-nav__link--active' : '' }}"
                               href="{{ route($item['route']) }}"
                               @if($section === $item['key']) aria-current="page" @endif>
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <div class="admin-sidebar__footer">
                <span class="admin-user">{{ auth('admin')->user()?->name }}</span>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="button button--ghost button--sm">
                        {{ __('blue.admin.sign_out') }}
                    </button>
                </form>
            </div>
        </aside>

        <div class="admin-shell__content">
            <main class="admin-main">
                @if(session('status'))
                    <div class="admin-flash" role="status">{{ session('status') }}</div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
