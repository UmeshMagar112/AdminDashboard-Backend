<?php
// app/Http/Resources/Product/ProductImageResource.php
namespace App\Http\Resources\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductImageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'image'      => $this->image,
            'sort_order' => $this->sort_order,
            'is_primary' => (bool) $this->is_primary,
        ];
    }
}
