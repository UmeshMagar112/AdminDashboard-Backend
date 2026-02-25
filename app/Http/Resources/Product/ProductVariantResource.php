<?php
// app/Http/Resources/Product/ProductVariantResource.php
namespace App\Http\Resources\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'product_id'    => $this->product_id,
            'sku'           => $this->sku,
            'name'          => $this->name, // "Red / XL"
            'price'         => $this->price ? (float) $this->price : null,
            'compare_price' => $this->compare_price ? (float) $this->compare_price : null,
            'image'         => $this->image,
            'status'        => $this->status,
            'attribute_values' => $this->whenLoaded('attributeValues', function () {
                return $this->attributeValues->map(fn($av) => [
                    'id'        => $av->id,
                    'value'     => $av->value,
                    'attribute' => ['id' => $av->attribute->id, 'name' => $av->attribute->name],
                ]);
            }),
            'inventory' => $this->whenLoaded('inventory', fn() => new InventoryResource($this->inventory)),
        ];
    }
}
