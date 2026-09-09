<?php

namespace App\Domains\Portfolio\Actions;

use App\Core\Activity\ActivityLogger;
use App\Core\Media\MediaService;
use App\Core\Slug\SlugService;
use App\Domains\Portfolio\Models\PortfolioProject;
use Illuminate\Http\UploadedFile;

class CreatePortfolioProject
{
    public function __construct(
        private readonly SlugService $slugs,
        private readonly MediaService $media,
        private readonly ActivityLogger $activity,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data  validated request data
     */
    public function __invoke(array $data): PortfolioProject
    {
        $status = (string) ($data['status'] ?? PortfolioProject::STATUS_DRAFT);
        $publishedAt = $data['published_at'] ?? null;

        if ($status === PortfolioProject::STATUS_PUBLISHED && $publishedAt === null) {
            $publishedAt = now();
        }

        $project = PortfolioProject::query()->create([
            'title' => $data['title'],
            'slug' => $this->slugs->make(
                (string) ($data['slug'] ?: $data['title']),
                PortfolioProject::class,
            ),
            'summary' => $data['summary'] ?? null,
            'challenge' => $data['challenge'] ?? null,
            'solution' => $data['solution'] ?? null,
            'results' => $data['results'] ?? null,
            'status' => $status,
            'featured' => (bool) ($data['featured'] ?? false),
            'published_at' => $publishedAt,
        ]);

        $project->technologies()->sync($data['technology_ids'] ?? []);

        if (isset($data['cover']) && $data['cover'] instanceof UploadedFile) {
            $this->media->attach($project, $data['cover'], 'covers', replace: true);
        }

        foreach (($data['gallery'] ?? []) as $file) {
            if ($file instanceof UploadedFile) {
                $this->media->attach($project, $file, 'gallery');
            }
        }

        $this->activity->record(
            auth('admin')->user(),
            'portfolio.created',
            $project,
            ['status' => $project->status],
        );

        return $project;
    }
}
