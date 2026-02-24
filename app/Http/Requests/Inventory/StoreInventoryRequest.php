<?php
// app/Http/Requests/Inventory/StoreInventoryRequest.php
namespace App\Http\Requests\Inventory;
use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'product_id'          => ['required', 'exists:products,id'],
            'product_variant_id'  => ['nullable', 'exists:product_variants,id'],
            'quantity'            => ['required', 'integer', 'min:0'],
            'reserved_quantity'   => ['nullable', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
