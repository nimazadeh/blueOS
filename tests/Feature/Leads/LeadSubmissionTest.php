<?php

use App\Domains\Leads\Models\Lead;
use App\Models\ActivityLog;

it('stores a public lead submission', function () {
    $this->from(route('home'))
        ->post(route('leads.store'), [
            'name' => 'Interested Operator',
            'email' => 'operator@example.com',
            'phone' => '+47 000 00 000',
            'company' => 'Example Co',
            'project_type' => 'saas',
            'message' => 'We need a real system for a real workflow.',
        ])
        ->assertRedirect(route('home'))
        ->assertSessionHas('lead_submitted');

    $lead = Lead::query()->where('email', 'operator@example.com')->firstOrFail();

    expect($lead->status)->toBe(Lead::STATUS_NEW)
        ->and($lead->source)->toBe(Lead::SOURCE_WEBSITE)
        ->and($lead->company)->toBe('Example Co');

    expect(ActivityLog::query()->where('action', 'lead.created')->exists())->toBeTrue();
});

it('validates the public submission', function () {
    $this->from(route('home'))
        ->post(route('leads.store'), [
            'name' => '',
            'email' => 'not-an-email',
            'message' => '',
        ])
        ->assertSessionHasErrors(['name', 'email', 'message']);

    $this->assertDatabaseCount('leads', 0);
});
