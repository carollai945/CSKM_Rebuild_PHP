<?php
namespace App\Http\Requests\MasterData;
use Illuminate\Foundation\Http\FormRequest;
class UpdateTitleRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $id = $this->route('title')?->id;
        return [
            'code'      => ['sometimes', 'string', 'max:20', "unique:titles,code,{$id}"],
            'name'      => ['sometimes', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
