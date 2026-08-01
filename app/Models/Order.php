<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    private const STATUS_LABELS = [
        'pending' => 'Pending',
        'processing' => 'Processing',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled',
    ];

    private const STATUS_BADGE_CLASSES = [
        'pending' => 'bg-surface-container-highest text-on-surface-variant',
        'processing' => 'bg-tertiary text-on-tertiary',
        'shipped' => 'bg-secondary text-on-secondary',
        'delivered' => 'bg-primary text-on-primary',
        'cancelled' => 'bg-error-container text-on-error-container',
    ];

    protected $fillable = [
        'user_id',
        'order_number',
        'customer_name',
        'email',
        'phone',
        'shipping_address',
        'address_line_2',
        'city',
        'province',
        'postal_code',
        'subtotal',
        'shipping_fee',
        'total',
        'payment_method',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'shipping_fee' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst($this->status);
    }

    public function statusBadgeClass(): string
    {
        return self::STATUS_BADGE_CLASSES[$this->status] ?? self::STATUS_BADGE_CLASSES['pending'];
    }
}
