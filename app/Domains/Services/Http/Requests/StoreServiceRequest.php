<?php

namespace App\Domains\Services\Http\Requests;

use App\Domains\Services\Models\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Service::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:191', 'alpha_dash'],
            'short_description' => ['nullable', 'string', 'max:300'],
            'description' => ['nullable', 'string', 'max:10000'],
            'icon' => ['nullable', 'string', 'max:100'],
            'status' => ['required', Rule::in(Service::STATUSES)],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ];
    }
}
