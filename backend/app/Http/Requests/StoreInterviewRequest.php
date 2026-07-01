<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInterviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lead_id'           => 'required|exists:leads,id',
            'staff_id'          => 'required|exists:staff,id',
            'interview_date'    => 'required|date',
            'result_code'       => 'required|string|max:50',
            'content'           => 'nullable|string',
            'next_contact_date' => 'nullable|date',
        ];
    }
}
