<?php

namespace App\Http\Controllers;

use App\Core\Seo\MetaResolver;
use App\Domains\Portfolio\Models\PortfolioProject;
use App\Domains\Products\Models\Product;
use App\Domains\Services\Models\Service;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Public homepage — powered by the database (Phase 2).
     *
     * Only published content surfaces; empty collections render the design
     * system empty states rather than invented content.
     */
    public function __invoke(MetaResolver $resolver): View
    {
        $products = Product::query()
            ->published()
            ->with('media')
            ->orderByDesc('featured')
            ->orderByDesc('published_at')
            ->limit(6)
            ->get();

        $services = Service::query()
            ->published()
            ->ordered()
            ->limit(6)
            ->get();

        $projects = PortfolioProject::query()
            ->published()
            ->with('media')
            ->orderByDesc('featured')
            ->orderByDesc('published_at')
            ->limit(6)
            ->get();

        $meta = $resolver->resolve(__('blue.home.title'), [
            'description' => __('blue.home.meta_description'),
            'og_type' => 'website',
            'canonical' => route('home'),
        ]);

        return view('home', [
            'meta' => $meta,
            'products' => $products,
            'services' => $services,
            'projects' => $projects,
        ]);
    }
}
