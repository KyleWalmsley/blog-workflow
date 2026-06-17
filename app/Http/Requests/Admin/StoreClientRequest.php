<?php

namespace App\Http\Requests\Admin;

use App\Enums\ClientStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'website' => ['nullable', 'url', 'max:255'],
            'business_description' => ['nullable', 'string'],
            'primary_keywords' => ['nullable', 'string'],
            'secondary_keywords' => ['nullable', 'string'],
            'target_locations' => ['nullable', 'string'],
            'target_audience' => ['nullable', 'string'],
            'tone_of_voice' => ['nullable', 'string'],
            'status' => ['required', Rule::enum(ClientStatus::class)],
            'notes' => ['nullable', 'string'],
        ];
    }
}
