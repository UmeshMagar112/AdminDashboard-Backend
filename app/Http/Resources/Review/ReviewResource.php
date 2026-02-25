<?php
// app/Http/Resources/Review/ReviewResource.php
namespace App\Http\Resources\Review;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                   => $this->id,
            'product_id'           => $this->product_id,
            'user_id'              => $this->user_id,
            'order_id'             => $this->order_id,
            'product'              => $this->whenLoaded('product', fn() => [
                'id'        => $this->product->id,
                'name'      => $this->product->name,
                'thumbnail' => $this->product->thumbnail,
            ]),
            'user'                 => $this->whenLoaded('user', fn() => [
                'id'     => $this->user->id,
                'name'   => $this->user->name,
                'avatar' => $this->user->avatar,
            ]),
            'rating'               => $this->rating,
            'title'                => $this->title,
            'body'                 => $this->body,
            'status'               => $this->status,
            'status_label'         => match($this->status) {
                0 => 'Pending', 1 => 'Approved', 2 => 'Rejected', default => 'Unknown'
            },
            'is_verified_purchase' => $this->is_verified_purchase,
            'created_at'           => $this->created_at?->toDateTimeString(),
            'updated_at'           => $this->updated_at?->toDateTimeString(),
        ];
    }
}
