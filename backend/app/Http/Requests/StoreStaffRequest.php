<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'staff_no' => ['required', 'string', 'max:20', 'unique:staff,staff_no'],
            'name' => ['required', 'string', 'max:100'],
            'abbr' => ['nullable', 'string', 'max:20'],
            'region_id' => ['nullable', 'integer', 'exists:regions,id'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'title_id' => ['nullable', 'integer', 'exists:titles,id'],
            'join_date' => ['nullable', 'date'],
            'leave_date' => ['nullable', 'date', 'after_or_equal:join_date'],
            'status' => ['required', 'string', Rule::in(['ACTIVE', 'INACTIVE', 'LEFT'])],
        ];
    }
}
