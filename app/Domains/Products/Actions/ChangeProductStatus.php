<?php

namespace App\Domains\Products\Actions;

use App\Core\Activity\ActivityLogger;
use App\Domains\Products\Models\Product;
use Illuminate\Validation\ValidationException;

class ChangeProductStatus
{
    public function __construct(
        private readonly ActivityLogger $activity,
    ) {
    }

    public function __invoke(Product $product, string $status): Product
    {
        if (! in_array($status, Product::STATUSES, true)) {
            throw ValidationException::withMessages([
                'status' => 'Unknown product status.',
            ]);
        }

        $wasPublished = $product->isPublished();

        $product->status = $status;
        $product->published_at = $status === Product::STATUS_PUBLISHED
            ? ($product->published_at ?? now())
            : ($wasPublished ? null : $product->published_at);

        $product->save();

        $this->activity->record(
            auth('admin')->user(),
            'product.status_changed',
            $product,
            ['status' => $status],
        );

        return $product;
    }
}
