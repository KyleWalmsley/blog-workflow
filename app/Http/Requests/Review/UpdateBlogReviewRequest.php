<?php

namespace App\Http\Requests\Review;

use App\Enums\BlogStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBlogReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(BlogStatus::class)],
            'client_notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
