<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password'      => 'required|string',
            'new_password'          => 'required|string|min:8|different:current_password',
            'new_password_confirmation' => 'required|same:new_password',
        ];
    }

    public function messages(): array
    {
        return [
            'new_password.different' => '新密碼不能與舊密碼相同。',
            'new_password_confirmation.same' => '新密碼與確認密碼不一致。',
        ];
    }
}
