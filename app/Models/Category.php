<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'image',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    // Auto-generate slug
    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    // Relationships

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeParentOnly($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeQueryFilter($query, $search)
    {
        if (empty($search)) return $query;
        return $query->where('name', 'like', "%{$search}%");
    }

    public function scopeTrashed($query, $value)
    {
        if (!empty($value)) {
            return $query->onlyTrashed();
        }
        return $query;
    }
}
