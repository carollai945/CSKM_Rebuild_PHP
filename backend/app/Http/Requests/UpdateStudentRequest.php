<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'student_no'       => 'sometimes|required|string|max:50|unique:students,student_no,' . $this->route('student')->id,
            'name'             => 'sometimes|required|string|max:100',
            'gender'           => 'nullable|in:M,F',
            'region_id'        => 'nullable|exists:regions,id',
            'phone'            => 'nullable|string|max:50',
            'mobile'           => 'nullable|string|max:50',
            'email'            => 'nullable|email|max:100',
            'address'          => 'nullable|string|max:300',
            'birth_date'       => 'nullable|date',
            'company_name'     => 'nullable|string|max:100',
            'source_code'      => 'nullable|string|max:50',
            'level_code'       => 'nullable|string|max:50',
            'advisor_staff_id' => 'nullable|exists:staff,id',
            'status'           => 'nullable|in:ACTIVE,INACTIVE,GRADUATED',
        ];
    }
}
