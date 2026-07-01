<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInstituteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'institute_name' => ['required', 'string', 'max:200'],
            'institute_code' => ['nullable', 'string', 'max:20'],
            'region_id' => ['nullable', 'integer', 'exists:regions,id'],
            'status' => ['required', 'string', 'in:ACTIVE,INACTIVE'],
        ];
    }
}
