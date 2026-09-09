@php
    // Real destinations only (no fake links, no fake socials).
    $footerNav = [
        ['label' => __('blue.nav.products'), 'href' => route('products.index')],
        ['label' => __('blue.nav.services'), 'href' => route('services.index')],
        ['label' => __('blue.nav.portfolio'), 'href' => route('portfolio.index')],
    ];
@endphp

<footer class="site-footer">
    <div class="container">
        <div class="site-footer__grid">
            <div class="site-footer__brand">
                <a class="site-header__brand" href="{{ route('home') }}">
                    <span class="site-header__brand-mark" aria-hidden="true">B</span>
                    <span>{{ config('blue.site.name') }}</span>
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
                    @forelse($footerServices ?? collect() as $service)
                        <li><a href="{{ route('services.show', $service->slug) }}">{{ $service->title }}</a></li>
                    @empty
                        <li><a href="{{ route('services.index') }}">{{ __('blue.footer.all_services') }}</a></li>
                    @endforelse
                </ul>
            </nav>
        </div>

        <div class="site-footer__bottom">
            <span>&copy; {{ date('Y') }} {{ config('blue.site.name') }}</span>
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
