<?php

namespace App\Domains\Products\Actions;

use App\Core\Activity\ActivityLogger;
use App\Core\Media\MediaService;
use App\Domains\Products\Models\Product;

class DeleteProduct
{
    public function __construct(
        private readonly MediaService $media,
        private readonly ActivityLogger $activity,
    ) {
    }

    public function __invoke(Product $product): void
    {
        $this->activity->record(
            auth('admin')->user(),
            'product.deleted',
            $product,
            ['title' => $product->title],
        );

        // Remove binaries + registry rows before deleting the entity.
        foreach ($product->media as $file) {
            $this->media->deleteFile($file);
        }

        $product->delete();
    }
}
