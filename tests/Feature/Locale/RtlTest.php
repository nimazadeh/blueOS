<?php

use App\Support\Locale;

it('declares both locales and their directions', function () {
    expect(config('blue.locales.enabled'))->toContain('en')
        ->and(config('blue.locales.enabled'))->toContain('fa')
        ->and(Locale::direction('en'))->toBe('ltr')
        ->and(Locale::direction('fa'))->toBe('rtl')
        ->and(Locale::isRtl('fa'))->toBeTrue()
        ->and(Locale::isRtl('en'))->toBeFalse();
});

it('writes the correct lang and dir attributes for the default locale', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('<html lang="en" dir="ltr">', false);
});

it('renders RTL attributes when the Persian locale is active', function () {
    $this->withSession(['locale' => 'fa'])
        ->get(route('home'))
        ->assertOk()
        ->assertSee('<html lang="fa" dir="rtl">', false);
});

it('persists a locale switch via the POST route', function () {
    $this->withSession(['locale' => 'en'])
        ->post(route('locale.switch', 'fa'), [], ['Referer' => route('home')])
        ->assertSessionHas('locale', 'fa');
});

it('rejects unknown locales', function () {
    $this->post(route('locale.switch', 'xx'))
        ->assertNotFound();
});

it('exposes html language and browser glob helpers', function () {
    app()->setLocale('fa');

    expect(Locale::htmlLang())->toBe('fa')
        ->and(Locale::browserGlob())->toBe('fa-IR');
});
