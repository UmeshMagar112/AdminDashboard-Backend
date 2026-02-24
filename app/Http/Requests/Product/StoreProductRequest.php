<?php
// app/Http/Requests/Product/StoreProductRequest.php
namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'category_id'       => ['required', 'exists:categories,id'],
            'name'              => ['required', 'string', 'max:191'],
            'slug'              => ['nullable', 'string', 'unique:products,slug'],
            'sku'               => ['required', 'string', 'unique:products,sku'],
            'short_description' => ['nullable', 'string'],
            'description'       => ['nullable', 'string'],
            'price'             => ['required', 'numeric', 'min:0'],
            'compare_price'     => ['nullable', 'numeric', 'min:0'],
            'cost_price'        => ['nullable', 'numeric', 'min:0'],
            'thumbnail'         => ['nullable', 'string'],
            'status'            => ['nullable', 'boolean'],
            'is_featured'       => ['nullable', 'boolean'],
            'manage_stock'      => ['nullable', 'boolean'],

            // Images
            'images'            => ['nullable', 'array'],
            'images.*.image'    => ['required', 'string'],
            'images.*.sort_order' => ['nullable', 'integer'],
            'images.*.is_primary' => ['nullable', 'boolean'],

            // Variants
            'variants'                               => ['nullable', 'array'],
            'variants.*.sku'                         => ['required', 'string', 'distinct'],
            'variants.*.price'                       => ['nullable', 'numeric', 'min:0'],
            'variants.*.compare_price'               => ['nullable', 'numeric', 'min:0'],
            'variants.*.attribute_value_ids'         => ['required', 'array'],
            'variants.*.attribute_value_ids.*'       => ['exists:attribute_values,id'],
            'variants.*.inventory.quantity'          => ['nullable', 'integer', 'min:0'],
            'variants.*.inventory.low_stock_threshold' => ['nullable', 'integer', 'min:0'],

            // Inventory (for products without variants)
            'inventory.quantity'            => ['nullable', 'integer', 'min:0'],
            'inventory.low_stock_threshold' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
