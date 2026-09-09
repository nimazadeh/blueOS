<?php

namespace App\Domains\Products\Http\Requests;

use App\Domains\Products\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        $product = $this->route('product');

        return $this->user()?->can('update', $product) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:191', 'alpha_dash'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:10000'],
            'status' => ['required', Rule::in(Product::STATUSES)],
            'type' => ['required', Rule::in(Product::TYPES)],
            'featured' => ['sometimes', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'features' => ['nullable', 'array', 'max:10'],
            'features.*.title' => ['required_with:features', 'string', 'max:200'],
            'features.*.description' => ['nullable', 'string', 'max:1000'],
            'features.*.sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'technology_ids' => ['nullable', 'array', 'max:20'],
            'technology_ids.*' => ['integer', 'exists:technologies,id'],
            'cover' => ['nullable', 'image', 'mimetypes:image/jpeg,image/png,image/webp,image/avif', 'max:12288'],
            'gallery' => ['nullable', 'array', 'max:12'],
            'gallery.*' => ['image', 'mimetypes:image/jpeg,image/png,image/webp,image/gif', 'max:12288'],
        ];
    }
}
