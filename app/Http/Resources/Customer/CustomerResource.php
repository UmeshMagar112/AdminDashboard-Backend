<?php
// app/Http/Resources/Customer/CustomerResource.php
namespace App\Http\Resources\Customer;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'email'        => $this->email,
            'phone'        => $this->phone,
            'avatar'       => $this->avatar,
            'status'       => $this->status,
            'is_active'    => (bool) $this->status,
            'roles'        => $this->whenLoaded('roles', fn() => $this->roles->pluck('name')),
            'orders_count' => $this->whenCounted('orders'),
            'created_at'   => $this->created_at?->toDateTimeString(),
            'updated_at'   => $this->updated_at?->toDateTimeString(),
        ];
    }
}
