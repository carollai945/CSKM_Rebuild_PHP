<?php
namespace App\Http\Requests\MasterData;
use Illuminate\Foundation\Http\FormRequest;
class StoreTitleRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'code'      => ['required', 'string', 'max:20', 'unique:titles,code'],
            'name'      => ['required', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
