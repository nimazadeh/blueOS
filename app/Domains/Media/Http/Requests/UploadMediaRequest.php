<?php

namespace App\Domains\Media\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UploadMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Domains\Media\Models\Media::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        // SVG is intentionally excluded everywhere (see docs/media.md).
        return [
            'file' => [
                'required',
                'file',
                'image',
                'mimetypes:image/jpeg,image/png,image/webp,image/avif,image/gif',
                'max:12288',
            ],
            'collection' => [
                'required',
                Rule::in(['covers', 'gallery']),
            ],
            'alt_text' => ['nullable', 'string', 'max:255'],
        ];
    }
}
