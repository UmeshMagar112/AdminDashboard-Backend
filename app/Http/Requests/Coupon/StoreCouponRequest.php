<?php
// app/Http/Requests/Coupon/StoreCouponRequest.php
namespace App\Http\Requests\Coupon;
use Illuminate\Foundation\Http\FormRequest;

class StoreCouponRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'code'                    => ['required', 'string', 'unique:coupons,code', 'max:50'],
            'name'                    => ['required', 'string', 'max:191'],
            'description'             => ['nullable', 'string'],
            'type'                    => ['required', 'in:fixed,percentage'],
            'value'                   => ['required', 'numeric', 'min:0'],
            'minimum_order_amount'    => ['nullable', 'numeric', 'min:0'],
            'maximum_discount_amount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit'             => ['nullable', 'integer', 'min:1'],
            'usage_limit_per_user'    => ['nullable', 'integer', 'min:1'],
            'status'                  => ['nullable', 'boolean'],
            'is_single_use'           => ['nullable', 'boolean'],
            'starts_at'               => ['nullable', 'date'],
            'expires_at'              => ['nullable', 'date', 'after_or_equal:starts_at'],
        ];
    }
}
