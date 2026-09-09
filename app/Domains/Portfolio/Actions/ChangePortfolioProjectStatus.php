<?php

namespace App\Domains\Portfolio\Actions;

use App\Core\Activity\ActivityLogger;
use App\Domains\Portfolio\Models\PortfolioProject;
use Illuminate\Validation\ValidationException;

class ChangePortfolioProjectStatus
{
    public function __construct(
        private readonly ActivityLogger $activity,
    ) {
    }

    public function __invoke(PortfolioProject $project, string $status): PortfolioProject
    {
        if (! in_array($status, PortfolioProject::STATUSES, true)) {
            throw ValidationException::withMessages([
                'status' => 'Unknown portfolio status.',
            ]);
        }

        $wasPublished = $project->isPublished();

        $project->status = $status;
        $project->published_at = $status === PortfolioProject::STATUS_PUBLISHED
            ? ($project->published_at ?? now())
            : ($wasPublished ? null : $project->published_at);

        $project->save();

        $this->activity->record(
            auth('admin')->user(),
            'portfolio.status_changed',
            $project,
            ['status' => $status],
        );

        return $project;
    }
}
