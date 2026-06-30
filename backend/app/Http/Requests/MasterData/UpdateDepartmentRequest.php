<?php
namespace App\Http\Requests\MasterData;
use Illuminate\Foundation\Http\FormRequest;
class UpdateDepartmentRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $id = $this->route('department')?->id;
        return [
            'code'      => ['sometimes', 'string', 'max:20', "unique:departments,code,{$id}"],
            'name'      => ['sometimes', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
