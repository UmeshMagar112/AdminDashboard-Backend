<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'compare_price',
        'image',
        'status',
    ];

    protected $casts = [
        'price'         => 'decimal:2',
        'compare_price' => 'decimal:2',
        'status'        => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(
            AttributeValue::class,
            'product_variant_attribute_values',
            'product_variant_id',
            'attribute_value_id'
        )->with('attribute');
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }

    // Helper: get variant label like "Red / XL"
    public function getNameAttribute(): string
    {
        return $this->attributeValues->pluck('value')->join(' / ');
    }
}
