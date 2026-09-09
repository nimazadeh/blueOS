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

    /*
    |--------------------------------------------------------------------------
    | Service catalog (presentation)
    |--------------------------------------------------------------------------
    |
    | Real outcome-led offerings of Blue Studio (see docs/information-
    | architecture.md). This is presentation configuration until the Services
    | domain brings database-driven service records in a later phase. Never
    | invent capabilities here; extend only with actual studio offerings.
    |
    */

    'services' => [
        'preview' => [
            [
                'icon' => '◈',
                'title' => ['en' => 'Web Applications', 'fa' => 'وب‌اپلیکیشن‌ها'],
                'description' => [
                    'en' => 'Complex, production-grade web platforms built for real workloads.',
                    'fa' => 'پلتفرم‌های وب پیچیده و آمادهٔ تولید، ساخته‌شده برای بار کاری واقعی.',
                ],
            ],
            [
                'icon' => '▣',
                'title' => ['en' => 'SaaS Development', 'fa' => 'توسعهٔ ساس'],
                'description' => [
                    'en' => 'From architecture to launch: multi-tenant products with clean data models.',
                    'fa' => 'از معماری تا راه‌اندازی: محصولات چندمستأجری با مدل دادهٔ تمیز.',
                ],
            ],
            [
                'icon' => '▤',
                'title' => ['en' => 'Backend Systems', 'fa' => 'سیستم‌های بک‌اند'],
                'description' => [
                    'en' => 'APIs, integrations and internal systems that stay maintainable at scale.',
                    'fa' => 'ای‌پی‌آی‌ها، یکپارچه‌سازی‌ها و سیستم‌های داخلی که در مقیاس قابل‌نگهداری می‌مانند.',
                ],
            ],
            [
                'icon' => '⚙',
                'title' => ['en' => 'Automation & Bot Systems', 'fa' => 'اتوماسیون و ربات‌ها'],
                'description' => [
                    'en' => 'Workflow automation and Telegram bots that remove repetitive work.',
                    'fa' => 'اتوماسیون گردش‌کار و ربات‌های تلگرام که کار تکراری را حذف می‌کنند.',
                ],
            ],
            [
                'icon' => '◇',
                'title' => ['en' => 'API & AI Integrations', 'fa' => 'یکپارچه‌سازی ای‌پی‌آی و هوش مصنوعی'],
                'description' => [
                    'en' => 'Connecting products to the services they need — including AI features.',
                    'fa' => 'اتصال محصولات به سرویس‌های موردنیاز — از جمله قابلیت‌های هوش مصنوعی.',
                ],
            ],
            [
                'icon' => '▧',
                'title' => ['en' => 'Custom Software', 'fa' => 'نرم‌افزار سفارشی'],
                'description' => [
                    'en' => 'Purpose-built tools for teams whose business doesn\'t fit off-the-shelf.',
                    'fa' => 'ابزارهای خاص برای تیم‌هایی که کسب‌وکارشان با محصولات آماده جور نیست.',
                ],
            ],
        ],
    ],
];
