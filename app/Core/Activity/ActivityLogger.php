<?php

namespace App\Core\Activity;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Audit trail boundary (foundation).
 *
 * Every meaningful admin/business action should be recorded through this
 * service so auditability lives in one place. Phase 1A establishes the write
 * path; observers/hooks for each domain arrive with those domains.
 */
class ActivityLogger
{
    /**
     * Record an auditable event.
     *
     * @param  array<string, mixed>  $context  JSON-safe context; secrets and
     *                                         passwords are never logged
     */
    public function record(mixed $actor, string $action, ?Model $subject = null, array $context = []): ActivityLog
    {
        $entry = ActivityLog::query()->create([
            'actor_id' => $this->actorId($actor),
            'action' => $action,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'properties' => $context,
            'ip_address' => $this->ip(),
        ]);

        Log::info("activity.{$action}", [
            'actor_id' => $entry->actor_id,
            'subject' => $entry->subject_type ? "{$entry->subject_type}#{$entry->subject_id}" : null,
        ]);

        return $entry;
    }

    /**
     * Convenience wrapper for anonymous/system events.
     */
    public function log(string $action, ?Model $subject = null, array $context = []): ActivityLog
    {
        return $this->record(null, $action, $subject, $context);
    }

    private function actorId(mixed $actor): ?int
    {
        if ($actor instanceof Model) {
            return (int) $actor->getKey();
        }

        return is_int($actor) ? $actor : null;
    }

    private function ip(): ?string
    {
        $ip = request()->ip();

        return is_string($ip) ? $ip : null;
    }
}
