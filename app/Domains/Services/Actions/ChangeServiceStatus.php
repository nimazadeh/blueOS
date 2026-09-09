<?php

namespace App\Domains\Services\Actions;

use App\Core\Activity\ActivityLogger;
use App\Domains\Services\Models\Service;
use Illuminate\Validation\ValidationException;

class ChangeServiceStatus
{
    public function __construct(
        private readonly ActivityLogger $activity,
    ) {
    }

    public function __invoke(Service $service, string $status): Service
    {
        if (! in_array($status, Service::STATUSES, true)) {
            throw ValidationException::withMessages([
                'status' => 'Unknown service status.',
            ]);
        }

        $service->status = $status;
        $service->save();

        $this->activity->record(
            auth('admin')->user(),
            'service.status_changed',
            $service,
            ['status' => $status],
        );

        return $service;
    }
}
