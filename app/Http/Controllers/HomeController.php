<?php

namespace App\Http\Controllers;

use App\Core\Seo\MetaResolver;

class HomeController extends Controller
{
    /**
     * Foundation homepage. Phase 1A deliberately keeps this minimal —
     * the full public homepage arrives in Phase 1B/public experience phase.
     */
    public function __invoke(MetaResolver $resolver)
    {
        $meta = $resolver->resolve(__('blue.home.title'), [
            'description' => __('blue.home.description'),
            'og_type' => 'website',
            'canonical' => route('home'),
        ]);

        return view('home', ['meta' => $meta]);
    }
}
