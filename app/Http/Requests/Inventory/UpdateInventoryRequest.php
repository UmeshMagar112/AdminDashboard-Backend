<?php
// app/Http/Requests/Inventory/UpdateInventoryRequest.php
namespace App\Http\Requests\Inventory;
use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'quantity'            => ['sometimes', 'integer', 'min:0'],
            'reserved_quantity'   => ['nullable', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],

            // For stock adjustment
            'adjustment_type'     => ['nullable', 'in:purchase,adjustment,damage,return'],
            'adjustment_quantity' => ['nullable', 'integer'],
            'adjustment_note'     => ['nullable', 'string'],
        ];
    }
}
