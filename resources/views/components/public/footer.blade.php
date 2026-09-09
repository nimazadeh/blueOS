@php
    // Real, existing destinations only (Phase 1B rule: no fake links).
    $footerNav = [
        ['label' => __('blue.nav.products'), 'href' => '#products'],
        ['label' => __('blue.nav.services'), 'href' => '#services'],
        ['label' => __('blue.nav.portfolio'), 'href' => '#portfolio'],
    ];

    $footerServices = collect(config('blue.services.preview'))
        ->pluck(app()->getLocale())
        ->filter()
        ->values();
@endphp

<footer class="site-footer">
    <div class="container">
        <div class="site-footer__grid">
            <div class="site-footer__brand">
                <a class="site-header__brand" href="{{ route('home') }}">
                    <span class="site-header__brand-mark" aria-hidden="true">B</span>
                    <span>Blue Studio</span>
                </a>
                <p class="site-footer__tagline">{{ __('blue.footer.tagline') }}</p>
            </div>

            <nav aria-label="{{ __('blue.footer.navigation') }}">
                <h2 class="site-footer__heading">{{ __('blue.footer.navigation') }}</h2>
                <ul class="site-footer__list" role="list">
                    @foreach($footerNav as $item)
                        <li><a href="{{ $item['href'] }}">{{ $item['label'] }}</a></li>
                    @endforeach
                </ul>
            </nav>

            <nav aria-label="{{ __('blue.footer.services') }}">
                <h2 class="site-footer__heading">{{ __('blue.footer.services') }}</h2>
                <ul class="site-footer__list" role="list">
                    @foreach($footerServices as $service)
                        <li><a href="#services">{{ $service }}</a></li>
                    @endforeach
                </ul>
            </nav>
        </div>

        <div class="site-footer__bottom">
            <span>&copy; {{ date('Y') }} Blue Studio</span>
            <form method="POST" action="{{ route('locale.switch', ['locale' => app()->getLocale() === 'en' ? 'fa' : 'en']) }}"
                  class="locale-switcher" data-locale-switcher>
                @csrf
                <button type="submit" class="locale-switcher__button">
                    {{ app()->getLocale() === 'en' ? 'فارسی' : 'English' }}
                </button>
            </form>
        </div>
    </div>
</footer>
