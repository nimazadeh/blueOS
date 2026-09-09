<?php

use App\Domains\Portfolio\Models\PortfolioProject;
use App\Models\ActivityLog;

it('creates a case study and publishes it', function () {
    $admin = roleUser('admin');

    $this->actingAs($admin, 'admin')
        ->post(route('admin.portfolio.store'), [
            'title' => 'Real client system',
            'summary' => 'A real case study summary.',
            'challenge' => 'The client had an unreliable legacy process.',
            'solution' => 'We rebuilt it as a maintainable platform.',
            'results' => 'Downtime eliminated; team workflow improved.',
            'status' => 'draft',
        ])
        ->assertRedirect();

    $project = PortfolioProject::query()->where('title', 'Real client system')->firstOrFail();
    expect($project->status)->toBe('draft');

    $this->actingAs($admin, 'admin')
        ->post(route('admin.portfolio.publish', $project))
        ->assertRedirect();

    $project->refresh();
    expect($project->isPublished())->toBeTrue()
        ->and($project->published_at)->not->toBeNull();

    $this->get(route('portfolio.show', $project->slug))
        ->assertOk()
        ->assertSee('Real client system', false);

    expect(ActivityLog::query()->where('action', 'portfolio.status_changed')->exists())->toBeTrue();
});

it('updates a case study', function () {
    $admin = roleUser('admin');
    $project = PortfolioProject::factory()->draft()->create();

    $this->actingAs($admin, 'admin')
        ->put(route('admin.portfolio.update', $project), [
            'title' => 'Updated case study',
            'summary' => 'New summary.',
            'status' => 'review',
        ])
        ->assertRedirect();

    $project->refresh();
    expect($project->title)->toBe('Updated case study')
        ->and($project->status)->toBe('review');
});

it('deletes a case study with authorization', function () {
    $admin = roleUser('admin');
    $project = PortfolioProject::factory()->create();

    $this->actingAs($admin, 'admin')
        ->delete(route('admin.portfolio.destroy', $project))
        ->assertRedirect();

    $this->assertDatabaseMissing('portfolio_projects', ['id' => $project->id]);
});

it('hides drafts and shows published projects publicly', function () {
    PortfolioProject::factory()->create(['title' => 'Public Case']);
    PortfolioProject::factory()->draft()->create(['title' => 'Private Case']);

    $this->get(route('portfolio.index'))
        ->assertOk()
        ->assertSee('Public Case', false)
        ->assertDontSee('Private Case', false);
});
