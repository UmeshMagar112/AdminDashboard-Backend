<?php
// app/Http/Requests/Product/UpdateProductRequest.php
namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('id');
        return [
            'category_id'       => ['sometimes', 'nullable', 'exists:categories,id'],
            'name'              => ['sometimes', 'string', 'max:191'],
            'slug'              => ['nullable', 'string', 'unique:products,slug,' . $id],
            'sku'               => ['sometimes', 'string', 'max:191', 'unique:products,sku,' . $id],
            'short_description' => ['nullable', 'string'],
            'description'       => ['nullable', 'string'],
            'price'             => ['sometimes', 'numeric', 'min:0'],
            'compare_price'     => ['nullable', 'numeric', 'min:0'],
            'cost_price'        => ['nullable', 'numeric', 'min:0'],
            'thumbnail'         => ['nullable', 'string'],
            'status'            => ['nullable', 'boolean'],
            'is_featured'       => ['nullable', 'boolean'],
            'manage_stock'      => ['nullable', 'boolean'],
            'images'            => ['nullable', 'array'],
            'images.*.image'    => ['nullable', 'string'],
            'images.*.sort_order' => ['nullable', 'integer'],
            'images.*.is_primary' => ['nullable', 'boolean'],
            'inventory.quantity'            => ['nullable', 'integer', 'min:0'],
            'inventory.low_stock_threshold' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
