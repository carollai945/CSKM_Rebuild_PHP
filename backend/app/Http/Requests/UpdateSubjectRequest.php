<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject_name' => ['required', 'string', 'max:200'],
            'subject_code' => ['nullable', 'string', 'max:20'],
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'status' => ['required', 'string', 'in:ACTIVE,INACTIVE'],
        ];
    }
}
