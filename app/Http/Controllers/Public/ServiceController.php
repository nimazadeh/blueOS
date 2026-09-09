<?php

namespace App\Http\Controllers\Public;

use App\Core\Seo\MetaResolver;
use App\Domains\Services\Models\Service;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(MetaResolver $resolver): View
    {
        $services = Service::query()
            ->published()
            ->ordered()
            ->get();

        return view('public.services.index', [
            'services' => $services,
            'meta' => $resolver->resolve(__('blue.services.title'), [
                'description' => __('blue.services.meta_description'),
                'canonical' => route('services.index'),
                'og_type' => 'website',
            ]),
        ]);
    }

    public function show(string $slug, MetaResolver $resolver): View
    {
        $service = Service::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('public.services.show', [
            'service' => $service,
            'meta' => $resolver->resolve($service->title, [
                'description' => $service->short_description ?? $service->title,
                'canonical' => route('services.show', $service->slug),
                'og_type' => 'article',
            ]),
        ]);
    }
}
