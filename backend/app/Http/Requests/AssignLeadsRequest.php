<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignLeadsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lead_ids'          => 'required|array|min:1',
            'lead_ids.*'        => 'integer|exists:leads,id',
            'to_staff_id'       => 'required|exists:staff,id',
        ];
    }
}
