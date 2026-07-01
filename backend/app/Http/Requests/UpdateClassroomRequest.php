<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClassroomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'region_id' => ['sometimes', 'nullable', 'integer', 'exists:regions,id'],
            'classroom_name' => ['sometimes', 'required', 'string', 'max:200'],
            'capacity' => ['sometimes', 'required', 'integer', 'min:1'],
            'status' => ['sometimes', 'required', 'string', 'in:ACTIVE,INACTIVE'],
        ];
    }
}
