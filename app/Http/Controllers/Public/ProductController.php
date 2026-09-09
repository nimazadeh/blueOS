<?php

namespace App\Http\Controllers\Public;

use App\Core\Seo\MetaResolver;
use App\Domains\Products\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(MetaResolver $resolver): View
    {
        $products = Product::query()
            ->published()
            ->with('media')
            ->orderByDesc('featured')
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('public.products.index', [
            'products' => $products,
            'meta' => $resolver->resolve(__('blue.products.title'), [
                'description' => __('blue.products.meta_description'),
                'canonical' => route('products.index'),
                'og_type' => 'website',
            ]),
        ]);
    }

    public function show(string $slug, MetaResolver $resolver): View
    {
        $product = Product::query()
            ->published()
            ->with('features', 'technologies', 'media')
            ->where('slug', $slug)
            ->firstOrFail();

        return view('public.products.show', [
            'product' => $product,
            'meta' => $resolver->resolve($product->title, [
                'description' => $product->excerpt ?? $product->title,
                'canonical' => route('products.show', $product->slug),
                'og_type' => 'article',
                'og_image' => $product->coverUrl(),
            ]),
        ]);
    }
}
