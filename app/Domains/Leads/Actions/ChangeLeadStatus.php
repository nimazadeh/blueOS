<?php

namespace App\Domains\Leads\Actions;

use App\Core\Activity\ActivityLogger;
use App\Domains\Leads\Models\Lead;
use Illuminate\Validation\ValidationException;

class ChangeLeadStatus
{
    public function __construct(
        private readonly ActivityLogger $activity,
    ) {
    }

    public function __invoke(Lead $lead, string $status): Lead
    {
        if (! in_array($status, Lead::STATUSES, true)) {
            throw ValidationException::withMessages([
                'status' => 'Unknown lead status.',
            ]);
        }

        if ($lead->status === $status) {
            return $lead;
        }

        $old = $lead->status;

        $lead->status = $status;
        $lead->save();

        $lead->statusHistory()->create([
            'old_status' => $old,
            'new_status' => $status,
            'user_id' => auth('admin')->id(),
        ]);

        $this->activity->record(
            auth('admin')->user(),
            'lead.status_changed',
            $lead,
            ['old_status' => $old, 'new_status' => $status],
        );

        return $lead;
    }
}
