<?php

use App\Core\Activity\ActivityLogger;
use App\Models\ActivityLog;
use App\Models\User;

it('records an activity entry for an actor and subject', function () {
    $user = User::factory()->create();
    $subject = User::factory()->create();

    $entry = app(ActivityLogger::class)->record($user, 'login', $subject, ['remember' => false]);

    expect($entry)->toBeInstanceOf(ActivityLog::class)
        ->and($entry->actor_id)->toBe($user->id)
        ->and($entry->subject_id)->toBe($subject->id)
        ->and($entry->action)->toBe('login')
        ->and($entry->properties)->toBe(['remember' => false]);
});

it('records system events without an actor', function () {
    $entry = app(ActivityLogger::class)->log('system.boot');

    expect($entry)->toBeInstanceOf(ActivityLog::class)
        ->and($entry->actor_id)->toBeNull()
        ->and($entry->action)->toBe('system.boot');
});
