<?php

use App\Domains\Services\Models\Service;

it('shows published services on the homepage and hides drafts', function () {
    Service::factory()->create(['title' => 'Real Service', 'sort_order' => 0]);
    Service::factory()->draft()->create(['title' => 'Hidden Service']);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Real Service', false)
        ->assertDontSee('Hidden Service', false);
});

it('keeps services ordered by sort_order', function () {
    Service::factory()->create(['title' => 'Second', 'sort_order' => 2]);
    Service::factory()->create(['title' => 'First', 'sort_order' => 1]);

    $this->get(route('home'))
        ->assertOk();

    $page = $this->get(route('services.index'))->getContent();
    expect(strpos($page, 'First') < strpos($page, 'Second'))->toBeTrue();
});

it('creates and publishes a service from the admin', function () {
    $admin = roleUser('admin');

    $this->actingAs($admin, 'admin')
        ->post(route('admin.services.store'), [
            'title' => 'Automation systems',
            'short_description' => 'Real automation for real workflows.',
            'status' => 'draft',
            'sort_order' => 3,
        ])
        ->assertRedirect();

    $service = Service::query()->where('title', 'Automation systems')->firstOrFail();
    expect($service->sort_order)->toBe(3);

    $this->actingAs($admin, 'admin')
        ->post(route('admin.services.publish', $service))
        ->assertRedirect();

    $this->get(route('services.show', $service->slug))
        ->assertOk()
        ->assertSee('Automation systems', false);
});
