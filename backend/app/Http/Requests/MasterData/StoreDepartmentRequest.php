<?php
namespace App\Http\Requests\MasterData;
use Illuminate\Foundation\Http\FormRequest;
class StoreDepartmentRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'code'      => ['required', 'string', 'max:20', 'unique:departments,code'],
            'name'      => ['required', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
