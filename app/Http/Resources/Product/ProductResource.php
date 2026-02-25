<?php
// app/Http/Resources/Product/ProductResource.php
namespace App\Http\Resources\Product;
use App\Http\Resources\Category\CategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                => $this->id,
            'category_id'       => $this->category_id,
            'category'          => $this->whenLoaded('category', fn() => new CategoryResource($this->category)),
            'name'              => $this->name,
            'slug'              => $this->slug,
            'sku'               => $this->sku,
            'short_description' => $this->short_description,
            'description'       => $this->description,
            'price'             => (float) $this->price,
            'compare_price'     => $this->compare_price ? (float) $this->compare_price : null,
            'cost_price'        => $this->cost_price ? (float) $this->cost_price : null,
            'thumbnail'         => $this->thumbnail,
            'status'            => $this->status,
            'is_featured'       => $this->is_featured,
            'manage_stock'      => $this->manage_stock,
            'images'            => $this->whenLoaded('images', fn() => ProductImageResource::collection($this->images)),
            'variants'          => $this->whenLoaded('variants', fn() => ProductVariantResource::collection($this->variants)),
            'inventory'         => $this->whenLoaded('inventory', fn() => new InventoryResource($this->inventory)),
            'reviews_count'     => $this->whenCounted('reviews'),
            'orders_count'      => $this->whenCounted('orderItems'),
            'average_rating'    => $this->whenLoaded('reviews', fn() => round($this->reviews->avg('rating'), 1)),
            'created_at'        => $this->created_at?->toDateTimeString(),
            'updated_at'        => $this->updated_at?->toDateTimeString(),
        ];
    }
}
