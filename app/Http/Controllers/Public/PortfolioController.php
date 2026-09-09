<?php

namespace App\Http\Controllers\Public;

use App\Core\Seo\MetaResolver;
use App\Domains\Portfolio\Models\PortfolioProject;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function index(MetaResolver $resolver): View
    {
        $projects = PortfolioProject::query()
            ->published()
            ->with('media')
            ->orderByDesc('featured')
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('public.portfolio.index', [
            'projects' => $projects,
            'meta' => $resolver->resolve(__('blue.portfolio.title'), [
                'description' => __('blue.portfolio.meta_description'),
                'canonical' => route('portfolio.index'),
                'og_type' => 'website',
            ]),
        ]);
    }

    public function show(string $slug, MetaResolver $resolver): View
    {
        $project = PortfolioProject::query()
            ->published()
            ->with('technologies', 'gallery', 'media')
            ->where('slug', $slug)
            ->firstOrFail();

        return view('public.portfolio.show', [
            'project' => $project,
            'meta' => $resolver->resolve($project->title, [
                'description' => $project->summary ?? $project->title,
                'canonical' => route('portfolio.show', $project->slug),
                'og_type' => 'article',
                'og_image' => $project->coverUrl(),
            ]),
        ]);
    }
}
