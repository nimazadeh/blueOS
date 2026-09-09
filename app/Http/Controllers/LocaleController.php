<?php

namespace App\Http\Controllers;

use App\Support\Locale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Persists the user's locale preference (session + cookie) and returns to
 * the previous page. Only enabled locales are accepted.
 */
class LocaleController extends Controller
{
    public function __invoke(Request $request, string $locale): RedirectResponse
    {
        abort_unless(Locale::isEnabled($locale), 404);

        $request->session()->put('locale', $locale);
        $request->session()->reflash();

        return redirect()
            ->back(302)
            ->withCookie(
                cookie('blue_locale', $locale, 60 * 24 * 365, '/', null, false, false)
            );
    }
}
