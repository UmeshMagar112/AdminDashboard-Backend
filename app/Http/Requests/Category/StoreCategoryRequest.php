<?php
// app/Http/Requests/Category/StoreCategoryRequest.php
namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'parent_id'   => ['nullable', 'exists:categories,id'],
            'name'        => ['required', 'string', 'max:191'],
            'slug'        => ['nullable', 'string', 'max:191', 'unique:categories,slug'],
            'description' => ['nullable', 'string'],
            'image'       => ['nullable', 'string'],
            'status'      => ['nullable', 'boolean'],
            'sort_order'  => ['nullable', 'integer'],
        ];
    }
}
