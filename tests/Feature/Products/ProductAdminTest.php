<?php

use App\Domains\Products\Models\Product;
use App\Models\ActivityLog;

it('creates a product with features and technologies', function () {
    $admin = roleUser('admin');
    $technologyId = \App\Domains\Products\Models\Technology::factory()->create()->id;

    $this->actingAs($admin, 'admin')
        ->post(route('admin.products.store'), [
            'title' => 'Launchpad Admin',
            'excerpt' => 'A real product with a real description.',
            'description' => 'Full description of the product for the detail page.',
            'status' => 'draft',
            'type' => 'saas',
            'featured' => '1',
            'features' => [
                ['title' => 'Team workspaces', 'sort_order' => 0],
                ['title' => 'Audit trail', 'description' => 'Append-only log', 'sort_order' => 1],
            ],
            'technology_ids' => [$technologyId],
        ])
        ->assertRedirect();

    $product = Product::query()->where('title', 'Launchpad Admin')->firstOrFail();

    expect($product->slug)->not->toBeEmpty()
        ->and($product->status)->toBe('draft')
        ->and($product->featured)->toBeTrue()
        ->and($product->features->count())->toBe(2)
        ->and($product->technologies->pluck('id'))->toContain($technologyId);

    expect(ActivityLog::query()->where('action', 'product.created')->exists())->toBeTrue();
});

it('validates product input', function () {
    $admin = roleUser('admin');

    $this->actingAs($admin, 'admin')
        ->post(route('admin.products.store'), [
            'status' => 'not-a-status',
            'type' => 'bad-type',
        ])
        ->assertSessionHasErrors(['title', 'status', 'type']);
});

it('updates a product and keeps the slug stable', function () {
    $admin = roleUser('admin');
    $product = Product::factory()->draft()->create(['slug' => 'stable-slug']);

    $this->actingAs($admin, 'admin')
        ->put(route('admin.products.update', $product), [
            'title' => 'Renamed product',
            'slug' => 'stable-slug',
            'status' => 'review',
            'type' => 'software',
        ])
        ->assertRedirect();

    $product->refresh();

    expect($product->slug)->toBe('stable-slug')
        ->and($product->status)->toBe('review');
});

it('publishes and unpublishes through the workflow actions', function () {
    $admin = roleUser('admin');
    $product = Product::factory()->draft()->create();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.products.publish', $product))
        ->assertRedirect();

    $product->refresh();
    expect($product->isPublished())->toBeTrue()
        ->and($product->published_at)->not->toBeNull();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.products.unpublish', $product))
        ->assertRedirect();

    $product->refresh();
    expect($product->status)->toBe(Product::STATUS_DRAFT)
        ->and($product->published_at)->toBeNull();
});

it('requires the publish permission for the publish endpoint', function () {
    $editor = roleUser('editor');
    $product = Product::factory()->draft()->create();

    $this->actingAs($editor, 'admin')
        ->post(route('admin.products.publish', $product))
        ->assertForbidden();
});
