<?php

namespace App\Support;

use Illuminate\Support\Facades\App;

/**
 * Locale helpers shared by views, middleware and controllers.
 *
 * This is the single place that knows about direction and HTML language
 * attributes, so RTL support cannot drift between templates.
 */
final class Locale
{
    /**
     * HTML `lang` attribute for the given locale (de-DE style).
     */
    public static function htmlLang(?string $locale = null): string
    {
        $locale ??= App::getLocale();

        return str_replace('_', '-', $locale);
    }

    /**
     * Text direction for a locale.
     */
    public static function direction(?string $locale = null): string
    {
        $locale ??= App::getLocale();
        $directions = config('blue.locales.directions', []);

        return $directions[$locale] ?? 'ltr';
    }

    /**
     * Whether the given locale uses right-to-left layout.
     */
    public static function isRtl(?string $locale = null): bool
    {
        return self::direction($locale) === 'rtl';
    }

    /**
     * Whether the locale is enabled on the platform.
     */
    public static function isEnabled(string $locale): bool
    {
        return in_array($locale, config('blue.locales.enabled', []), true);
    }

    /**
     * BCP-47 accept-language value for a locale, e.g. "fa-IR".
     */
    public static function browserGlob(?string $locale = null): string
    {
        $locale ??= App::getLocale();

        return in_array($locale, ['fa', 'fa-IR'], true) ? 'fa-IR' : self::htmlLang($locale);
    }
}
