<?php

namespace App\Domains\Leads\Actions;

use App\Core\Activity\ActivityLogger;
use App\Domains\Leads\Models\Lead;
use App\Domains\Leads\Models\LeadNote;

class AddLeadNote
{
    public function __construct(
        private readonly ActivityLogger $activity,
    ) {
    }

    public function __invoke(Lead $lead, string $note): LeadNote
    {
        $entry = $lead->notes()->create([
            'note' => $note,
            'user_id' => auth('admin')->id(),
        ]);

        $this->activity->record(
            auth('admin')->user(),
            'lead.note_added',
            $lead,
        );

        return $entry;
    }
}
