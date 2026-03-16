<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number', 'user_id', 'status', 'payment_status',
        'payment_method', 'payment_reference',
        'subtotal', 'discount_amount', 'shipping_amount', 'tax_amount', 'total',
        'shipping_name', 'shipping_email', 'shipping_phone',
        'shipping_address', 'shipping_city', 'shipping_state',
        'shipping_zip', 'shipping_country', 'notes', 'shipped_at', 'delivered_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(uniqid());
            }
        });
    }

    // Relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }



    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->latest();
    }

    // Scopes

    public function scopeQueryFilter($query, $search)
    {
        if (empty($search))
            return $query;
        return $query->where(fn($q) => $q->where('order_number', 'like', "%{$search}%")
        ->orWhere('shipping_name', 'like', "%{$search}%")
        ->orWhere('shipping_email', 'like', "%{$search}%"));
    }

    public function scopeStatusFilter($query, $status)
    {
        if (empty($status))
            return $query;
        return $query->where('status', $status);
    }

    public function scopeTrashed($query, $value)
    {
        if (!empty($value)) {
            return $query->onlyTrashed();
        }
        return $query;
    }
}
