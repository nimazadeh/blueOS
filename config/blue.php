<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Site
    |--------------------------------------------------------------------------
    |
    | Static identity of Blue Studio. Values here are framework defaults and
    | may be overridden through Settings (database) in later phases. Do not
    | place secrets or environment-specific values in this file.
    |
    */

    'site' => [
        'name' => env('APP_NAME', 'Blue Studio'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Locales
    |--------------------------------------------------------------------------
    |
    | The platform is bilingual from the start: English (default) and Persian
    | (RTL). The default locale is served unprefixed; other enabled locales
    | are served from a /{locale} prefix. Adding a locale here and to
    | APP_ENABLED_LOCALES must be paired with translation files and RTL checks.
    |
    */

    'locales' => [
        'default' => env('APP_LOCALE', 'en'),

        // Locales available on the platform, in priority order.
        'enabled' => array_filter(array_map(
            'trim',
            explode(',', env('APP_ENABLED_LOCALES', 'en,fa')),
        )),

        // Direction per locale. `rtl` locales are mirrored via CSS logical
        // properties, never via transforms or overrides.
        'directions' => [
            'en' => 'ltr',
            'fa' => 'rtl',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | SEO defaults
    |--------------------------------------------------------------------------
    |
    | Fallbacks used by App\Core\Seo\MetaResolver when no entity metadata
    | exists yet. Content-level SEO records arrive with the domain phases.
    |
    */

    'seo' => [
        'defaults' => [
            'title_suffix' => 'Blue Studio',
            'description' => 'Blue Studio builds digital products, custom software and reliable systems.',
            'canonical_host' => null, // enforced from APP_URL when null
            'robots' => 'index,follow',
            'og_type' => 'website',
            'twitter_card' => 'summary_large_image',
        ],
    ],

];
