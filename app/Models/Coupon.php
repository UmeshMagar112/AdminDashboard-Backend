<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'minimum_order_amount',
        'maximum_discount_amount',
        'usage_limit',
        'usage_limit_per_user',
        'used_count',
        'status',
        'is_single_use',
        'starts_at',
        'expires_at',
    ];

    protected $casts = [
        'value'                   => 'decimal:2',
        'minimum_order_amount'    => 'decimal:2',
        'maximum_discount_amount' => 'decimal:2',
        'status'                  => 'boolean',
        'is_single_use'           => 'boolean',
        'starts_at'               => 'datetime',
        'expires_at'              => 'datetime',
    ];

    protected $appends = ['is_valid', 'is_expired'];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // ─── Computed Attributes ──────────────────────────────────────────────────

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getIsValidAttribute(): bool
    {
        if (!$this->status) return false;
        if ($this->is_expired) return false;
        if ($this->starts_at && $this->starts_at->isFuture()) return false;
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;
        return true;
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeValid($query)
    {
        return $query->where('status', 1)
                     ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
                     ->where(fn($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()));
    }

    public function scopeQueryFilter($query, $search)
    {
        if (empty($search)) return $query;
        return $query->where(fn($q) => $q->where('code', 'like', "%{$search}%")
                                          ->orWhere('name', 'like', "%{$search}%"));
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function calculateDiscount(float $orderTotal): float
    {
        if ($orderTotal < $this->minimum_order_amount) return 0;

        $discount = $this->type === 'percentage'
            ? ($orderTotal * $this->value / 100)
            : $this->value;

        if ($this->maximum_discount_amount) {
            $discount = min($discount, $this->maximum_discount_amount);
        }

        return min($discount, $orderTotal);
    }
}
