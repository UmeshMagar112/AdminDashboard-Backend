<?php
// app/Http/Resources/Coupon/CouponResource.php
namespace App\Http\Resources\Coupon;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                      => $this->id,
            'code'                    => $this->code,
            'name'                    => $this->name,
            'description'             => $this->description,
            'type'                    => $this->type,
            'value'                   => (float) $this->value,
            'minimum_order_amount'    => (float) $this->minimum_order_amount,
            'maximum_discount_amount' => $this->maximum_discount_amount ? (float) $this->maximum_discount_amount : null,
            'usage_limit'             => $this->usage_limit,
            'usage_limit_per_user'    => $this->usage_limit_per_user,
            'used_count'              => $this->used_count,
            'status'                  => $this->status,
            'is_single_use'           => $this->is_single_use,
            'is_valid'                => $this->is_valid,
            'is_expired'              => $this->is_expired,
            'starts_at'               => $this->starts_at?->toDateTimeString(),
            'expires_at'              => $this->expires_at?->toDateTimeString(),
            'orders_count'            => $this->whenCounted('orders'),
            'created_at'              => $this->created_at?->toDateTimeString(),
            'updated_at'              => $this->updated_at?->toDateTimeString(),
        ];
    }
}
