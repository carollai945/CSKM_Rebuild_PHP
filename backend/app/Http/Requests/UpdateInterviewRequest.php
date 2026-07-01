<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInterviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'interview_date'    => 'sometimes|required|date',
            'result_code'       => 'sometimes|required|string|max:50',
            'content'           => 'nullable|string',
            'next_contact_date' => 'nullable|date',
        ];
    }
}
