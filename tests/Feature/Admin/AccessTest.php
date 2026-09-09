<?php

use App\Domains\Products\Models\Product;
use App\Models\User;

it('redirects guests away from every admin section', function () {
    $this->get(route('admin.products.index'))->assertRedirect(route('admin.login'));
    $this->get(route('admin.settings'))->assertRedirect(route('admin.login'));
    $this->get(route('admin.activity'))->assertRedirect(route('admin.login'));
});

it('let the owner bypass permission checks', function () {
    $owner = roleUser('owner');
    $product = Product::factory()->create();

    $this->actingAs($owner, 'admin')
        ->delete(route('admin.products.destroy', $product))
        ->assertRedirect(route('admin.products.index'));

    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});

it('lets an editor create content but not delete it', function () {
    $editor = roleUser('editor');

    $this->actingAs($editor, 'admin')
        ->post(route('admin.products.store'), [
            'title' => 'Studio internal tooling',
            'status' => 'draft',
            'type' => 'software',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('products', ['title' => 'Studio internal tooling']);

    $product = Product::query()->where('title', 'Studio internal tooling')->firstOrFail();

    $this->actingAs($editor, 'admin')
        ->delete(route('admin.products.destroy', $product))
        ->assertForbidden();
});

it('does not let an editor manage settings or view activity', function () {
    $editor = roleUser('editor');

    $this->actingAs($editor, 'admin')
        ->patch(route('admin.settings.update'), ['site.name' => 'Hacked'])
        ->assertForbidden();

    $this->actingAs($editor, 'admin')
        ->get(route('admin.activity'))
        ->assertForbidden();
});

it('gives editor the leads and media permissions', function () {
    $editor = roleUser('editor');

    expect($editor->hasPermission('leads.manage'))->toBeTrue()
        ->and($editor->hasPermission('media.manage'))->toBeTrue()
        ->and($editor->hasPermission('settings.manage'))->toBeFalse();
});

it('records role assignments on users', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    expect($user->hasRole('admin'))->toBeTrue()
        ->and($user->hasPermission('products.create'))->toBeTrue();
});
