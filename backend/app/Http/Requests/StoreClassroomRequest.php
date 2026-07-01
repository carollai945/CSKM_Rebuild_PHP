<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClassroomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'region_id' => ['nullable', 'integer', 'exists:regions,id'],
            'classroom_name' => ['required', 'string', 'max:200'],
            'capacity' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'string', 'in:ACTIVE,INACTIVE'],
        ];
    }
}
