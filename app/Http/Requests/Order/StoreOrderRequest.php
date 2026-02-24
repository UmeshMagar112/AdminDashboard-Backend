<?php
// ─── Order Requests ───────────────────────────────────────────────────────────

// app/Http/Requests/Order/StoreOrderRequest.php
namespace App\Http\Requests\Order;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'user_id'          => ['required', 'exists:users,id'],
            'coupon_code'      => ['nullable', 'exists:coupons,code'],
            'payment_method'   => ['nullable', 'in:cash_on_delivery,stripe,paypal,bank_transfer'],
            'items'            => ['required', 'array', 'min:1'],
            'items.*.product_id'         => ['required', 'exists:products,id'],
            'items.*.product_variant_id' => ['nullable', 'exists:product_variants,id'],
            'items.*.quantity'           => ['required', 'integer', 'min:1'],
            'shipping_name'    => ['required', 'string'],
            'shipping_email'   => ['required', 'email'],
            'shipping_phone'   => ['nullable', 'string'],
            'shipping_address' => ['required', 'string'],
            'shipping_city'    => ['required', 'string'],
            'shipping_state'   => ['nullable', 'string'],
            'shipping_zip'     => ['nullable', 'string'],
            'shipping_country' => ['required', 'string'],
            'notes'            => ['nullable', 'string'],
        ];
    }
}
