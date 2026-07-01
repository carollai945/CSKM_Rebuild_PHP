<?php

namespace App\Http\Requests;

use App\Models\Staff;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStaffStatusRequest extends FormRequest
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
            'status' => ['required', 'string', Rule::in(['ACTIVE', 'INACTIVE', 'LEFT'])],
            'leave_date' => [
                'nullable',
                'date',
                static function (string $attribute, mixed $value, Closure $fail) use ($staff): void {
                    if ($value === null || $staff->join_date === null) {
                        return;
                    }

                    if (strtotime((string) $value) < strtotime($staff->join_date->toDateString())) {
                        $fail('The leave date field must be a date after or equal to join date.');
                    }
                },
            ],
        ];
    }
}
