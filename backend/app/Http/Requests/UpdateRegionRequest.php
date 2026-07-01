<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRegionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'region_code' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('regions', 'region_code')->ignore($this->route('region')),
            ],
            'region_name' => ['sometimes', 'required', 'string', 'max:100'],
            'region_english_name' => ['nullable', 'string', 'max:100'],
            'abbr' => ['nullable', 'string', 'max:20'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
        ];
    }
}
