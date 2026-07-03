<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBlueprintRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
       return [
            'name' => ['sometimes', 'string', 'max:100'],
            'target_audience' => ['sometimes', 'string', 'max:255'],
            'tone' => ['sometimes', 'string', 'max:255'],
            'max_characters' => ['sometimes', 'integer', 'min:50', 'max:280'],
            'max_hashtags' => ['sometimes', 'integer', 'min:0', 'max:10'],
        ];
    }
}
