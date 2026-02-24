<?php
// app/Http/Requests/Coupon/UpdateCouponRequest.php
namespace App\Http\Requests\Coupon;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCouponRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'code'                    => ['sometimes', 'string', 'unique:coupons,code,' . $this->route('id'), 'max:50'],
            'name'                    => ['sometimes', 'string', 'max:191'],
            'description'             => ['nullable', 'string'],
            'type'                    => ['sometimes', 'in:fixed,percentage'],
            'value'                   => ['sometimes', 'numeric', 'min:0'],
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
