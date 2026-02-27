<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'product_variant_id',
        'quantity',
        'reserved_quantity',
        'low_stock_threshold',
    ];

    protected $appends = ['available_quantity', 'is_low_stock', 'is_out_of_stock'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    // ─── Computed Attributes ──────────────────────────────────────────────────

    public function getAvailableQuantityAttribute(): int
    {
        return max(0, $this->quantity - $this->reserved_quantity);
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->available_quantity > 0 && $this->available_quantity <= $this->low_stock_threshold;
    }

    public function getIsOutOfStockAttribute(): bool
    {
        return $this->available_quantity <= 0;
    }

    // Scopes 
    public function scopeLowStock($query)
    {
        return $query->whereRaw('(quantity - reserved_quantity) <= low_stock_threshold')
                     ->whereRaw('(quantity - reserved_quantity) > 0');
    }

    public function scopeOutOfStock($query)
    {
        return $query->whereRaw('(quantity - reserved_quantity) <= 0');
    }

    public function scopeQueryFilter($query, $search)
    {
        if (empty($search)) return $query;
        return $query->whereHas('product', fn($q) => $q->where('name', 'like', "%{$search}%")
                                                        ->orWhere('sku', 'like', "%{$search}%"));
    }
}
