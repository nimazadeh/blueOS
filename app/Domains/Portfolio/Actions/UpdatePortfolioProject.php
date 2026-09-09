<?php

namespace App\Domains\Portfolio\Actions;

use App\Core\Activity\ActivityLogger;
use App\Core\Media\MediaService;
use App\Core\Slug\SlugService;
use App\Domains\Portfolio\Models\PortfolioProject;
use Illuminate\Http\UploadedFile;

class UpdatePortfolioProject
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
    public function __invoke(PortfolioProject $project, array $data): PortfolioProject
    {
        $status = (string) ($data['status'] ?? $project->status);
        $publishedAt = $data['published_at'] ?? $project->published_at;

        if ($status === PortfolioProject::STATUS_PUBLISHED && $publishedAt === null) {
            $publishedAt = now();
        }

        $project->fill([
            'title' => $data['title'],
            'slug' => $this->slugs->make(
                (string) ($data['slug'] ?: $data['title']),
                PortfolioProject::class,
                $project->getKey(),
            ),
            'summary' => $data['summary'] ?? null,
            'challenge' => $data['challenge'] ?? null,
            'solution' => $data['solution'] ?? null,
            'results' => $data['results'] ?? null,
            'status' => $status,
            'featured' => (bool) ($data['featured'] ?? $project->featured),
            'published_at' => $publishedAt,
        ])->save();

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
            'portfolio.updated',
            $project,
            ['status' => $project->status],
        );

        return $project;
    }
}
