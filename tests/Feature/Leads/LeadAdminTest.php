<?php

use App\Domains\Leads\Models\Lead;
use App\Models\ActivityLog;

it('changes lead status and records history', function () {
    $admin = roleUser('admin');
    $lead = Lead::factory()->create();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.leads.status', $lead), ['status' => 'reviewing'])
        ->assertRedirect();

    $lead->refresh();
    expect($lead->status)->toBe('reviewing');

    $history = $lead->statusHistory()->first();
    expect($history->old_status)->toBe('new')
        ->and($history->new_status)->toBe('reviewing')
        ->and($history->user_id)->toBe($admin->id);

    expect(ActivityLog::query()->where('action', 'lead.status_changed')->exists())->toBeTrue();
});

it('rejects unknown lead statuses', function () {
    $admin = roleUser('admin');
    $lead = Lead::factory()->create();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.leads.status', $lead), ['status' => 'impossible'])
        ->assertSessionHasErrors('status');
});

it('adds notes to a lead', function () {
    $admin = roleUser('admin');
    $lead = Lead::factory()->create();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.leads.notes', $lead), ['note' => 'Called the operator; verifying scope.'])
        ->assertRedirect();

    expect($lead->notes()->first()->note)->toBe('Called the operator; verifying scope.')
        ->and($lead->notes()->first()->user_id)->toBe($admin->id);
});

it('filters the lead list by status', function () {
    $admin = roleUser('admin');
    Lead::factory()->create(['name' => 'New Lead']);
    Lead::factory()->status('won')->create(['name' => 'Won Lead']);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.leads.index', ['status' => 'won']))
        ->assertOk()
        ->assertSee('Won Lead', false)
        ->assertDontSee('New Lead', false);
});
