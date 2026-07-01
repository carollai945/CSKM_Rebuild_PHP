<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfessorRequest extends FormRequest
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
            'specialty'         => 'nullable|string|max:200',
            'photo_path'        => 'nullable|string|max:500',
            'status'            => 'nullable|in:ACTIVE,INACTIVE',
            'document_file_names'   => 'nullable|array',
            'document_file_names.*' => 'string|max:500',
        ];
    }
}
