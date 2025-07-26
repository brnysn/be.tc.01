<?php

namespace App\Models;

use App\OrderStatuses;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Order model
 *
 * @property int $id
 * @property int $user_id
 * @property OrderStatuses $status
 * @property float $total_price
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \App\Models\User $user
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $items
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'total_price',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public static $sortable = [
        'total_price',
        'created_at',
        'updated_at',
    ];

    protected static function boot()
    {
        parent::boot();

        // Global scope to filter orders by customer role
        static::addGlobalScope('customerOrders', function ($builder) {
            if (auth()->check() && auth()->user()->isCustomer()) {
                $builder->where('user_id', auth()->id());
            }
        });

        static::creating(function ($order) {
            $order->user_id = $order->user_id ?? auth()->id();
            $order->total_price = $order->calculateTotalPrice();
            $order->status = OrderStatuses::Pending->label();
        });
    }

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Helpers
     */
    public function calculateTotalPrice(): float
    {
        return $this->items->sum(fn ($item) => $item->unit_price * $item->quantity);
    }

    public function isPending(): bool
    {
        return $this->status === OrderStatuses::Pending->label();
    }

    public function isApproved(): bool
    {
        return $this->status === OrderStatuses::Approved->label();
    }

    public function isShipped(): bool
    {
        return $this->status === OrderStatuses::Shipped->label();
    }
}
