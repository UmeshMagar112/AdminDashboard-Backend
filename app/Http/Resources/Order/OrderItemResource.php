<?php
// app/Http/Resources/Order/OrderItemResource.php
namespace App\Http\Resources\Order;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                 => $this->id,
            'product_id'         => $this->product_id,
            'product_variant_id' => $this->product_variant_id,
            'product_name'       => $this->product_name,
            'variant_name'       => $this->variant_name,
            'sku'                => $this->sku,
            'quantity'           => $this->quantity,
            'unit_price'         => (float) $this->unit_price,
            'total_price'        => (float) $this->total_price,
        ];
    }
}
