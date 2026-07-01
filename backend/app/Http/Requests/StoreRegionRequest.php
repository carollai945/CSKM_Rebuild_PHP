<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'region_code' => ['required', 'string', 'max:50', 'unique:regions,region_code'],
            'region_name' => ['required', 'string', 'max:100'],
            'region_english_name' => ['nullable', 'string', 'max:100'],
            'abbr' => ['nullable', 'string', 'max:20'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
        ];
    }
}
