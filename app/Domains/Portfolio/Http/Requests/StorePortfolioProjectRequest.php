<?php

namespace App\Domains\Portfolio\Http\Requests;

use App\Domains\Portfolio\Models\PortfolioProject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePortfolioProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', PortfolioProject::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:191', 'alpha_dash'],
            'summary' => ['nullable', 'string', 'max:500'],
            'challenge' => ['nullable', 'string', 'max:20000'],
            'solution' => ['nullable', 'string', 'max:20000'],
            'results' => ['nullable', 'string', 'max:20000'],
            'status' => ['required', Rule::in(PortfolioProject::STATUSES)],
            'featured' => ['sometimes', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'technology_ids' => ['nullable', 'array', 'max:20'],
            'technology_ids.*' => ['integer', 'exists:technologies,id'],
            'cover' => ['nullable', 'image', 'mimetypes:image/jpeg,image/png,image/webp,image/avif', 'max:12288'],
            'gallery' => ['nullable', 'array', 'max:12'],
            'gallery.*' => ['image', 'mimetypes:image/jpeg,image/png,image/webp,image/gif', 'max:12288'],
        ];
    }
}
