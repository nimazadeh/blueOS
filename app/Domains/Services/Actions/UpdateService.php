<?php

namespace App\Domains\Services\Actions;

use App\Core\Activity\ActivityLogger;
use App\Core\Slug\SlugService;
use App\Domains\Services\Models\Service;

class UpdateService
{
    public function __construct(
        private readonly SlugService $slugs,
        private readonly ActivityLogger $activity,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data  validated request data
     */
    public function __invoke(Service $service, array $data): Service
    {
        $service->fill([
            'title' => $data['title'],
            'slug' => $this->slugs->make(
                (string) ($data['slug'] ?: $data['title']),
                Service::class,
                $service->getKey(),
            ),
            'short_description' => $data['short_description'] ?? null,
            'description' => $data['description'] ?? null,
            'icon' => array_key_exists('icon', $data) ? $data['icon'] : $service->icon,
            'status' => $data['status'] ?? $service->status,
            'sort_order' => (int) ($data['sort_order'] ?? $service->sort_order),
        ])->save();

        $this->activity->record(
            auth('admin')->user(),
            'service.updated',
            $service,
            ['status' => $service->status],
        );

        return $service;
    }
}
