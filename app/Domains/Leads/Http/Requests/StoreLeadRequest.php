<?php

namespace App\Domains\Leads\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Public lead submission from the website lead-generation form.
 */
class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'company' => ['nullable', 'string', 'max:120'],
            'project_type' => ['nullable', 'string', 'max:60'],
            'message' => ['required', 'string', 'max:4000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => __('blue.leads.validation.name_required'),
            'email.required' => __('blue.leads.validation.email_required'),
            'email.email' => __('blue.leads.validation.email_invalid'),
            'message.required' => __('blue.leads.validation.message_required'),
        ];
    }
}
