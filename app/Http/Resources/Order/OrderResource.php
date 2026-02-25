<?php
// app/Http/Resources/Order/OrderResource.php
namespace App\Http\Resources\Order;
use App\Http\Resources\Customer\CustomerResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'order_number'     => $this->order_number,
            'user_id'          => $this->user_id,
            'customer'         => $this->whenLoaded('user', fn() => new CustomerResource($this->user)),
            'coupon_id'        => $this->coupon_id,
            'coupon'           => $this->whenLoaded('coupon', fn() => $this->coupon ? [
                'id'   => $this->coupon->id,
                'code' => $this->coupon->code,
                'name' => $this->coupon->name,
            ] : null),
            'status'           => $this->status,
            'payment_status'   => $this->payment_status,
            'payment_method'   => $this->payment_method,
            'payment_reference'=> $this->payment_reference,
            'subtotal'         => (float) $this->subtotal,
            'discount_amount'  => (float) $this->discount_amount,
            'shipping_amount'  => (float) $this->shipping_amount,
            'tax_amount'       => (float) $this->tax_amount,
            'total'            => (float) $this->total,
            'shipping' => [
                'name'    => $this->shipping_name,
                'email'   => $this->shipping_email,
                'phone'   => $this->shipping_phone,
                'address' => $this->shipping_address,
                'city'    => $this->shipping_city,
                'state'   => $this->shipping_state,
                'zip'     => $this->shipping_zip,
                'country' => $this->shipping_country,
            ],
            'notes'            => $this->notes,
            'items'            => $this->whenLoaded('items', fn() => OrderItemResource::collection($this->items)),
            'items_count'      => $this->whenCounted('items'),
            'status_histories' => $this->whenLoaded('statusHistories', fn() => $this->statusHistories->map(fn($h) => [
                'status'     => $h->status,
                'comment'    => $h->comment,
                'created_by' => $h->creator?->name,
                'created_at' => $h->created_at?->toDateTimeString(),
            ])),
            'shipped_at'       => $this->shipped_at?->toDateTimeString(),
            'delivered_at'     => $this->delivered_at?->toDateTimeString(),
            'created_at'       => $this->created_at?->toDateTimeString(),
            'updated_at'       => $this->updated_at?->toDateTimeString(),
        ];
    }
}
