<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
{ 
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'quantity'            => $this->quantity,
            'low_stock_threshold' => $this->low_stock_threshold,
        ];
    }
}
