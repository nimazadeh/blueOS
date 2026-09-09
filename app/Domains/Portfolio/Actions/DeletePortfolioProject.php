<?php

namespace App\Domains\Portfolio\Actions;

use App\Core\Activity\ActivityLogger;
use App\Core\Media\MediaService;
use App\Domains\Portfolio\Models\PortfolioProject;

class DeletePortfolioProject
{
    public function __construct(
        private readonly MediaService $media,
        private readonly ActivityLogger $activity,
    ) {
    }

    public function __invoke(PortfolioProject $project): void
    {
        $this->activity->record(
            auth('admin')->user(),
            'portfolio.deleted',
            $project,
            ['title' => $project->title],
        );

        foreach ($project->media as $file) {
            $this->media->deleteFile($file);
        }

        $project->delete();
    }
}
