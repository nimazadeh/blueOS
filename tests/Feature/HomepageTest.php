<?php

use App\Core\Seo\MetaResolver;

it('serves the foundation homepage', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Blue Studio OS', false);
});

it('renders SEO metadata in the document head', function () {
    $meta = (new MetaResolver())->resolve('Blue Studio OS');

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('<title>'.e($meta->title).'</title>', false)
        ->assertSee('meta name="description"', false);
});

it('serves a 404 page for unknown routes', function () {
    $this->get('/definitely-not-a-real-route')
        ->assertNotFound();
});
