<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'short_description',
        'description',
        'price',
        'compare_price',
        'cost_price',
        'thumbnail',
        'status',
        'is_featured',
        'manage_stock',
    ];

    protected $casts = [
        'price'         => 'decimal:2',
        'compare_price' => 'decimal:2',
        'cost_price'    => 'decimal:2',
        'status'        => 'boolean',
        'is_featured'   => 'boolean',
        'manage_stock'  => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', 1);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class)->whereNull('product_variant_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', 1);
    }

    public function scopeQueryFilter($query, $search)
    {
        if (empty($search)) return $query;
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%")
              ->orWhere('short_description', 'like', "%{$search}%");
        });
    }

    public function scopeCategoryIds($query, ...$ids)
    {
        $ids = array_filter(array_merge(...array_map(fn($id) => (array)$id, $ids)));
        if (empty($ids)) return $query;
        return $query->whereIn('category_id', $ids);
    }

    public function scopePriceRange($query, $range)
    {
        if (empty($range)) return $query;
        [$min, $max] = explode(',', $range);
        return $query->whereBetween('price', [(float)$min, (float)$max]);
    }
}
