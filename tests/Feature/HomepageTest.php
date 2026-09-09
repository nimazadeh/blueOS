<?php

use App\Core\Seo\MetaResolver;
use Illuminate\Support\Facades\Route;

it('serves the Phase 1B homepage', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Blue Studio OS', false)
        ->assertSee('data-locale-switcher', false)
        ->assertSee('data-nav-toggle', false);
});

it('renders SEO metadata in the document head', function () {
    $meta = (new MetaResolver())->resolve('Blue Studio OS');

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('<title>'.e($meta->title).'</title>', false)
        ->assertSee('meta name="description"', false)
        ->assertSee('rel="canonical"', false)
        ->assertSee('property="og:type"', false);
});

it('renders the real services catalog as cards', function () {
    $count = count(config('blue.services.preview'));

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('id="services"', false)
        ->assertSee('service-card', false)
        ->assertSee(config('blue.services.preview.0.title.en'), false);
});

it('renders accessible empty states for products and portfolio (no fake content)', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('No products published yet', false)
        ->assertSee('No portfolio projects yet', false);
});

it('serves a 404 page for unknown routes', function () {
    $this->get('/definitely-not-a-real-route')
        ->assertNotFound();
});

it('enables the Persian locale through the public switcher', function () {
    $response = $this->from(route('home'))
        ->post(route('locale.switch', ['locale' => 'fa']));

    $response->assertRedirect(route('home'));
    $this->assertSame('fa', session('locale'));
});

it('renders the document in RTL for the Persian session locale', function () {
    $this->withSession(['locale' => 'fa'])
        ->get(route('home'))
        ->assertOk()
        ->assertSee('dir="rtl"', false)
        ->assertSee('lang="fa"', false);
});
