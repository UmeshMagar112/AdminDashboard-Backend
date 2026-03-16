<?php
// app/Http/Resources/Inventory/InventoryResource.php
namespace App\Http\Resources\Inventory;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
{
    public function toArray($request): array
    {
        $sku = null;
        if ($this->relationLoaded('variant') && $this->variant) {
            $sku = $this->variant->sku;
        } elseif ($this->relationLoaded('product') && $this->product) {
            $sku = $this->product->sku;
        }
        $productName = $this->relationLoaded('product') && $this->product ? $this->product->name : null;

        return [
            'id'                   => $this->id,
            'product_id'           => $this->product_id,
            'product_variant_id'   => $this->product_variant_id,
            'product_name'         => $productName,
            'sku'                  => $sku,
            'quantity'             => $this->quantity,
            'reserved_quantity'    => $this->reserved_quantity,
            'available_quantity'   => $this->available_quantity,
            'low_stock_threshold'  => $this->low_stock_threshold,
            'is_low_stock'         => $this->is_low_stock,
            'is_out_of_stock'      => $this->is_out_of_stock,
            'product'              => $this->whenLoaded('product', fn() => [
                'id'   => $this->product->id,
                'name' => $this->product->name,
                'sku'  => $this->product->sku,
                'thumbnail' => $this->product->thumbnail,
            ]),
            'variant'              => $this->whenLoaded('variant', fn() => $this->variant ? [
                'id'   => $this->variant->id,
                'name' => $this->variant->name,
                'sku'  => $this->variant->sku,
            ] : null),
            'updated_at'           => $this->updated_at?->toDateTimeString(),
        ];
    }
}
