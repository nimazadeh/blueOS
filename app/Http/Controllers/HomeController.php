<?php

namespace App\Http\Controllers;

use App\Core\Seo\MetaResolver;

class HomeController extends Controller
{
    /**
     * Public homepage v1.
     *
     * Phase 1B: presentation-only. Products and portfolio are intentionally
     * empty (real content arrives with their domains — nothing is fabricated);
     * services come from the real service catalog in config/blue.php.
     */
    public function __invoke(MetaResolver $resolver): \Illuminate\View\View
    {
        $locale = app()->getLocale();

        // Presentation data for the current locale only; views never resolve
        // translations themselves (architecture rule).
        $services = collect(config('blue.services.preview'))
            ->map(fn (array $service) => [
                'icon' => $service['icon'],
                'title' => $service['title'][$locale] ?? $service['title']['en'],
                'description' => $service['description'][$locale] ?? $service['description']['en'],
            ]);

        $meta = $resolver->resolve(__('blue.home.title'), [
            'description' => __('blue.home.meta_description'),
            'og_type' => 'website',
            'canonical' => route('home'),
        ]);

        return view('home', [
            'meta' => $meta,
            'services' => $services,
        ]);
    }
}
