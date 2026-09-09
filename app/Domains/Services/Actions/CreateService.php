<?php

namespace App\Domains\Services\Actions;

use App\Core\Activity\ActivityLogger;
use App\Core\Slug\SlugService;
use App\Domains\Services\Models\Service;

class CreateService
{
    public function __construct(
        private readonly SlugService $slugs,
        private readonly ActivityLogger $activity,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data  validated request data
     */
    public function __invoke(array $data): Service
    {
        $service = Service::query()->create([
            'title' => $data['title'],
            'slug' => $this->slugs->make(
                (string) ($data['slug'] ?: $data['title']),
                Service::class,
            ),
            'short_description' => $data['short_description'] ?? null,
            'description' => $data['description'] ?? null,
            'icon' => $data['icon'] ?? null,
            'status' => $data['status'] ?? Service::STATUS_DRAFT,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ]);

        $this->activity->record(
            auth('admin')->user(),
            'service.created',
            $service,
            ['status' => $service->status],
        );

        return $service;
    }
}
