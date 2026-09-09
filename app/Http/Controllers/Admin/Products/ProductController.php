<?php

namespace App\Http\Controllers\Admin\Products;

use App\Domains\Products\Actions\ChangeProductStatus;
use App\Domains\Products\Actions\CreateProduct;
use App\Domains\Products\Actions\DeleteProduct;
use App\Domains\Products\Actions\UpdateProduct;
use App\Domains\Products\Http\Requests\StoreProductRequest;
use App\Domains\Products\Http\Requests\UpdateProductRequest;
use App\Domains\Products\Models\Product;
use App\Domains\Products\Models\Technology;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Product::class);

        $status = $request->query('status');
        $status = is_string($status) && in_array($status, Product::STATUSES, true) ? $status : null;

        return view('admin.products.index', [
            'products' => Product::query()
                ->with('media')
                ->when($status, fn ($query) => $query->where('status', $status))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
            'statuses' => Product::STATUSES,
            'activeStatus' => $status,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Product::class);

        return view('admin.products.create', [
            'technologies' => Technology::query()->orderBy('name')->get(),
            'statuses' => Product::STATUSES,
            'types' => Product::TYPES,
        ]);
    }

    public function store(StoreProductRequest $request, CreateProduct $createProduct): RedirectResponse
    {
        $product = $createProduct($request->validated());

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('status', __('blue.admin.flash.created'));
    }

    public function edit(Product $product): View
    {
        $this->authorize('update', $product);

        return view('admin.products.edit', [
            'product' => $product->load('features', 'technologies', 'media'),
            'technologies' => Technology::query()->orderBy('name')->get(),
            'statuses' => Product::STATUSES,
            'types' => Product::TYPES,
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product, UpdateProduct $updateProduct): RedirectResponse
    {
        $updateProduct($product, $request->validated());

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('status', __('blue.admin.flash.updated'));
    }

    public function destroy(Product $product, DeleteProduct $deleteProduct): RedirectResponse
    {
        $this->authorize('delete', $product);

        $deleteProduct($product);

        return redirect()
            ->route('admin.products.index')
            ->with('status', __('blue.admin.flash.deleted'));
    }

    public function publish(Product $product, ChangeProductStatus $changeStatus): RedirectResponse
    {
        $this->authorize('publish', $product);

        $changeStatus($product, Product::STATUS_PUBLISHED);

        return back()->with('status', __('blue.admin.flash.published'));
    }

    public function unpublish(Product $product, ChangeProductStatus $changeStatus): RedirectResponse
    {
        $this->authorize('publish', $product);

        $changeStatus($product, Product::STATUS_DRAFT);

        return back()->with('status', __('blue.admin.flash.unpublished'));
    }
}
