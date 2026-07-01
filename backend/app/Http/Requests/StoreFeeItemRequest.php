<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeeItemRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'course_id'  => 'required|exists:courses,id',
            'item_code'  => 'required|string|max:50|unique:fee_items,item_code',
            'item_name'  => 'required|string|max:200',
            'amount'     => 'required|numeric|min:0',
            'currency'   => 'nullable|string|max:10',
            'status'     => 'nullable|in:ACTIVE,INACTIVE',
        ];
    }
}
