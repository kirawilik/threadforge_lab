<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBlueprintRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:100'],
            'target_audience' => ['required', 'string', 'max:255'],
        'tone' => ['required', 'string', 'max:255'],
        'max_characters' => ['required', 'integer', 'min:50', 'max:280'],
            'max_hashtags' => ['required', 'integer', 'min:0', 'max:10'],
        ];
    }
}
