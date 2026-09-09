<?php

namespace Database\Factories;

use App\Domains\Products\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->words(3, true);

        return [
            'title' => ucfirst($title),
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 9999),
            'excerpt' => fake()->sentence(14),
            'description' => fake()->paragraphs(3, true),
            'status' => Product::STATUS_PUBLISHED,
            'type' => 'software',
            'featured' => false,
            'published_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => Product::STATUS_DRAFT,
            'published_at' => null,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn () => ['featured' => true]);
    }
}
