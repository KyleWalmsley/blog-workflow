<?php

namespace App\Http\Requests\Review;

use App\Enums\CopySectionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSectionReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(CopySectionStatus::class)],
            'client_notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
