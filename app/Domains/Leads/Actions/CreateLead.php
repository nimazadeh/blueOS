<?php

namespace App\Domains\Leads\Actions;

use App\Core\Activity\ActivityLogger;
use App\Domains\Leads\Models\Lead;

class CreateLead
{
    public function __construct(
        private readonly ActivityLogger $activity,
    ) {
    }

    /**
     * Public + admin lead intake (source passed explicitly, never trusted
     * from the request).
     *
     * @param  array<string, mixed>  $data  validated request data
     */
    public function __invoke(array $data, string $source = Lead::SOURCE_WEBSITE): Lead
    {
        $lead = Lead::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'company' => $data['company'] ?? null,
            'project_type' => $data['project_type'] ?? null,
            'message' => $data['message'],
            'status' => Lead::STATUS_NEW,
            'source' => $source,
        ]);

        $this->activity->log('lead.created', $lead, [
            'source' => $source,
        ]);

        return $lead;
    }
}
