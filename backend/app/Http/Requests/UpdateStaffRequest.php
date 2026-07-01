<?php

namespace App\Http\Requests;

use App\Models\Staff;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Staff $staff */
        $staff = $this->route('staff');

        return [
            'staff_no' => ['sometimes', 'required', 'string', 'max:20', Rule::unique('staff', 'staff_no')->ignore($staff->id)],
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'abbr' => ['nullable', 'string', 'max:20'],
            'region_id' => ['nullable', 'integer', 'exists:regions,id'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'title_id' => ['nullable', 'integer', 'exists:titles,id'],
            'join_date' => ['nullable', 'date'],
            'leave_date' => [
                'nullable',
                'date',
                'after_or_equal:join_date',
                function (string $attribute, mixed $value, Closure $fail) use ($staff): void {
                    if ($value === null || $staff->join_date === null || $this->filled('join_date')) {
                        return;
                    }

                    if (strtotime((string) $value) < strtotime($staff->join_date->toDateString())) {
                        $fail('The leave date field must be a date after or equal to join date.');
                    }
                },
            ],
            'status' => ['sometimes', 'required', 'string', Rule::in(['ACTIVE', 'INACTIVE', 'LEFT'])],
        ];
    }
}
