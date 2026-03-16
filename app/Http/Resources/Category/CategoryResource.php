<?php
// ─── app/Http/Resources/Category/CategoryResource.php ────────────────────────
namespace App\Http\Resources\Category;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'parent_id'   => $this->parent_id,
            'parent'      => $this->whenLoaded('parent', fn() => new CategoryResource($this->parent)),
            'children'    => $this->whenLoaded('children', fn() => CategoryResource::collection($this->children)),
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => $this->description,
            'image'       => $this->image,
            'status'      => $this->status,
            'is_active'   => (bool) $this->status,
            'sort_order'  => $this->sort_order,
            'products_count' => $this->whenCounted('products'),
            'created_at'  => $this->created_at?->toDateTimeString(),
            'updated_at'  => $this->updated_at?->toDateTimeString(),
        ];
    }
}
