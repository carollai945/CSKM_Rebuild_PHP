<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'              => 'sometimes|required|string|max:100',
            'gender'            => 'nullable|in:M,F',
            'phone'             => 'nullable|string|max:50',
            'mobile'            => 'nullable|string|max:50',
            'email'             => 'nullable|email|max:100',
            'education_level'   => 'nullable|string|max:50',
            'education_other'   => 'nullable|string|max:100',
            'source_code'       => 'nullable|string|max:50',
            'region_id'         => 'nullable|exists:regions,id',
            'assigned_staff_id' => 'nullable|exists:staff,id',
            'status'            => 'nullable|in:NEW,CONTACTED,INTERESTED,NOT_INTERESTED,CONVERTED',
        ];
    }
}
