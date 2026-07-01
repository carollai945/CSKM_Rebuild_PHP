<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFeeItemRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'item_code'  => 'sometimes|required|string|max:50|unique:fee_items,item_code,' . $this->route('fee_item')->id,
            'item_name'  => 'sometimes|required|string|max:200',
            'amount'     => 'sometimes|required|numeric|min:0',
            'currency'   => 'nullable|string|max:10',
            'status'     => 'nullable|in:ACTIVE,INACTIVE',
        ];
    }
}
