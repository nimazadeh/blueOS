<?php

namespace App\Http\Controllers\Admin\Portfolio;

use App\Domains\Portfolio\Actions\ChangePortfolioProjectStatus;
use App\Domains\Portfolio\Actions\CreatePortfolioProject;
use App\Domains\Portfolio\Actions\DeletePortfolioProject;
use App\Domains\Portfolio\Actions\UpdatePortfolioProject;
use App\Domains\Portfolio\Http\Requests\StorePortfolioProjectRequest;
use App\Domains\Portfolio\Http\Requests\UpdatePortfolioProjectRequest;
use App\Domains\Portfolio\Models\PortfolioProject;
use App\Domains\Products\Models\Technology;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', PortfolioProject::class);

        $status = $request->query('status');
        $status = is_string($status) && in_array($status, PortfolioProject::STATUSES, true) ? $status : null;

        return view('admin.portfolio.index', [
            'projects' => PortfolioProject::query()
                ->with('media')
                ->when($status, fn ($query) => $query->where('status', $status))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
            'statuses' => PortfolioProject::STATUSES,
            'activeStatus' => $status,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', PortfolioProject::class);

        return view('admin.portfolio.create', [
            'technologies' => Technology::query()->orderBy('name')->get(),
            'statuses' => PortfolioProject::STATUSES,
        ]);
    }

    public function store(StorePortfolioProjectRequest $request, CreatePortfolioProject $createProject): RedirectResponse
    {
        $project = $createProject($request->validated());

        return redirect()
            ->route('admin.portfolio.edit', $project)
            ->with('status', __('blue.admin.flash.created'));
    }

    public function edit(PortfolioProject $project): View
    {
        $this->authorize('update', $project);

        return view('admin.portfolio.edit', [
            'project' => $project->load('technologies', 'media'),
            'technologies' => Technology::query()->orderBy('name')->get(),
            'statuses' => PortfolioProject::STATUSES,
        ]);
    }

    public function update(
        UpdatePortfolioProjectRequest $request,
        PortfolioProject $project,
        UpdatePortfolioProject $updateProject,
    ): RedirectResponse {
        $updateProject($project, $request->validated());

        return redirect()
            ->route('admin.portfolio.edit', $project)
            ->with('status', __('blue.admin.flash.updated'));
    }

    public function destroy(PortfolioProject $project, DeletePortfolioProject $deleteProject): RedirectResponse
    {
        $this->authorize('delete', $project);

        $deleteProject($project);

        return redirect()
            ->route('admin.portfolio.index')
            ->with('status', __('blue.admin.flash.deleted'));
    }

    public function publish(PortfolioProject $project, ChangePortfolioProjectStatus $changeStatus): RedirectResponse
    {
        $this->authorize('publish', $project);

        $changeStatus($project, PortfolioProject::STATUS_PUBLISHED);

        return back()->with('status', __('blue.admin.flash.published'));
    }

    public function unpublish(PortfolioProject $project, ChangePortfolioProjectStatus $changeStatus): RedirectResponse
    {
        $this->authorize('publish', $project);

        $changeStatus($project, PortfolioProject::STATUS_DRAFT);

        return back()->with('status', __('blue.admin.flash.unpublished'));
    }
}
