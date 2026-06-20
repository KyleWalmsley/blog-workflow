<?php

namespace App\Http\Requests\Admin;

use App\Enums\CopySectionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCopySectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $type = $this->input('section_type');

        return [
            'section_type' => ['required', Rule::enum(CopySectionType::class)],
            'title' => ['nullable', 'string', 'max:255'],
            'headline' => ['nullable', 'string', 'max:255'],
            'sub_headline' => ['nullable', 'string', 'max:255'],
            'content' => [
                in_array($type, ['about_us', 'about_page', 'service']) ? 'required' : 'nullable',
                'string',
            ],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
        ];
    }
}
