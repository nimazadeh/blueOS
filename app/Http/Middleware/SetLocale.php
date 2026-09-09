<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves the active application locale for every web request.
 *
 * Resolution order:
 *   1. Explicit {locale} URL prefix (handled before this middleware via
 *      route parameter — the route sets the locale through App\Support\Locale).
 *   2. Session preference set by the locale switcher.
 *   3. Accept-Language header (only for enabled locales).
 *   4. The configured default locale.
 *
 * The resolved locale is stored on the session so the switcher and future
 * route generation stay consistent.
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolve($request);

        app()->setLocale($locale);

        if (! $request->session()->has('locale')) {
            $request->session()->put('locale', $locale);
        }

        return $next($request);
    }

    private function resolve(Request $request): string
    {
        $enabled = config('blue.locales.enabled', ['en']);
        $default = config('blue.locales.default', 'en');

        $session = $request->session()->get('locale');

        if (is_string($session) && in_array($session, $enabled, true)) {
            return $session;
        }

        if ($request->header('Accept-Language') !== null) {
            foreach ($enabled as $candidate) {
                if (str_starts_with(strtolower((string) $request->header('Accept-Language')), strtolower($candidate))) {
                    return $candidate;
                }
            }
        }

        return $default;
    }
}
