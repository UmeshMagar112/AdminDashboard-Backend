<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id', 'user_id', 'order_id',
        'rating', 'title', 'body', 'status', 'is_verified_purchase',
    ];

    protected $casts = [
        'rating'               => 'integer',
        'status'               => 'integer',
        'is_verified_purchase' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeApproved($query)
    {
        return $query->where('status', 1);
    }

    public function scopePending($query)
    {
        return $query->where('status', 0);
    }

    public function scopeQueryFilter($query, $search)
    {
        if (empty($search)) return $query;
        return $query->where(fn($q) => $q->where('title', 'like', "%{$search}%")
                                          ->orWhere('body', 'like', "%{$search}%"));
    }

    public function scopeStatusFilter($query, $status)
    {
        if ($status === null || $status === '') return $query;
        return $query->where('status', $status);
    }

    public function scopeRatingFilter($query, $rating)
    {
        if (empty($rating)) return $query;
        return $query->where('rating', $rating);
    }
}
