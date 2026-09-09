<?php

namespace App\Domains\Products\Actions;

use App\Core\Activity\ActivityLogger;
use App\Core\Media\MediaService;
use App\Core\Slug\SlugService;
use App\Domains\Products\Models\Product;
use Illuminate\Http\UploadedFile;

/**
 * Create a product with its features, technologies and media.
 */
class CreateProduct
{
    public function __construct(
        private readonly SlugService $slugs,
        private readonly MediaService $media,
        private readonly ActivityLogger $activity,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data  validated request data
     */
    public function __invoke(array $data): Product
    {
        $status = (string) ($data['status'] ?? Product::STATUS_DRAFT);
        $publishedAt = $data['published_at'] ?? null;

        if ($status === Product::STATUS_PUBLISHED && $publishedAt === null) {
            $publishedAt = now();
        }

        $product = Product::query()->create([
            'title' => $data['title'],
            'slug' => $this->slugs->make(
                (string) ($data['slug'] ?: $data['title']),
                Product::class,
            ),
            'excerpt' => $data['excerpt'] ?? null,
            'description' => $data['description'] ?? null,
            'status' => $status,
            'type' => $data['type'] ?? 'software',
            'featured' => (bool) ($data['featured'] ?? false),
            'published_at' => $publishedAt,
        ]);

        $this->syncFeatures($product, $data['features'] ?? []);
        $product->technologies()->sync($data['technology_ids'] ?? []);

        if (isset($data['cover']) && $data['cover'] instanceof UploadedFile) {
            $this->media->attach($product, $data['cover'], 'covers', replace: true);
        }

        foreach (($data['gallery'] ?? []) as $file) {
            if ($file instanceof UploadedFile) {
                $this->media->attach($product, $file, 'gallery');
            }
        }

        $this->activity->record(
            auth('admin')->user(),
            'product.created',
            $product,
            ['status' => $product->status],
        );

        return $product;
    }

    /**
     * @param  array<int, array<string, mixed>>  $features
     */
    private function syncFeatures(Product $product, array $features): void
    {
        $rows = array_map(fn (array $feature, int $index) => [
            'title' => $feature['title'],
            'description' => $feature['description'] ?? null,
            'sort_order' => (int) ($feature['sort_order'] ?? $index),
        ], $features, array_keys($features));

        $product->features()->createMany($rows);
    }
}
