@php
    // Real destinations only (Phase 1B+ rule: no fake links).
    $navItems = [
        ['label' => __('blue.nav.products'), 'href' => route('products.index')],
        ['label' => __('blue.nav.portfolio'), 'href' => route('portfolio.index')],
        ['label' => __('blue.nav.services'), 'href' => route('services.index')],
    ];
@endphp

<header class="site-header">
    <div class="container site-header__inner">
        <a class="site-header__brand" href="{{ route('home') }}">
            <span class="site-header__brand-mark" aria-hidden="true">B</span>
            <span>{{ config('blue.site.name') }}</span>
        </a>

        <nav class="site-header__nav" aria-label="{{ __('blue.primary_navigation') }}">
            @foreach($navItems as $item)
                <a class="site-header__link" href="{{ $item['href'] }}">{{ $item['label'] }}</a>
            @endforeach
        </nav>

        <div class="site-header__actions">
            <x-ui.button href="{{ url('/#start-project') }}" size="sm" variant="primary">
                {{ __('blue.nav.start_project') }}
            </x-ui.button>
        </div>

        <button
            type="button"
            class="site-header__toggle"
            data-nav-toggle
            aria-controls="mobile-nav"
            aria-expanded="false"
            aria-label="{{ __('blue.nav.open_menu') }}"
        >
            <span class="visually-hidden">{{ __('blue.nav.open_menu') }}</span>
            <span class="site-header__toggle-icon" aria-hidden="true"></span>
        </button>
    </div>

    <x-ui.drawer id="mobile-nav" labelledby="mobile-nav-title" :title="__('blue.nav.menu')">
        <nav class="drawer-nav" aria-label="{{ __('blue.nav.mobile_navigation') }}">
            @foreach($navItems as $item)
                <a class="drawer-nav__link" href="{{ $item['href'] }}">{{ $item['label'] }}</a>
            @endforeach
            <p class="drawer-nav__section-label">{{ __('blue.nav.cta') }}</p>
            <a class="drawer-nav__link" href="{{ url('/#start-project') }}">{{ __('blue.nav.start_project') }}</a>
        </nav>
    </x-ui.drawer>
</header>
