<?php

namespace App\Domains\Services\Actions;

use App\Core\Activity\ActivityLogger;
use App\Domains\Services\Models\Service;

class DeleteService
{
    public function __construct(
        private readonly ActivityLogger $activity,
    ) {
    }

    public function __invoke(Service $service): void
    {
        $this->activity->record(
            auth('admin')->user(),
            'service.deleted',
            $service,
            ['title' => $service->title],
        );

        $service->delete();
    }
}
