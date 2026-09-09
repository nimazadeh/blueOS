<?php

use App\Domains\Products\Models\Product;

it('shows published products on the homepage and hides drafts', function () {
    Product::factory()->create(['title' => 'Visible Product']);
    Product::factory()->draft()->create(['title' => 'Hidden Draft']);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Visible Product', false)
        ->assertDontSee('Hidden Draft', false);
});

it('serves the product index page', function () {
    Product::factory()->create(['title' => 'Indexed Product']);

    $this->get(route('products.index'))
        ->assertOk()
        ->assertSee('Indexed Product', false);
});

it('serves a product detail page with SEO metadata', function () {
    $product = Product::factory()->create(['title' => 'SEO Product']);

    $this->get(route('products.show', $product->slug))
        ->assertOk()
        ->assertSee('<title>SEO Product', false)
        ->assertSee('rel="canonical"', false)
        ->assertSee('property="og:type" content="article"', false);
});

it('hides unpublished products from public pages', function () {
    $product = Product::factory()->draft()->create();

    $this->get(route('products.show', $product->slug))->assertNotFound();
});
