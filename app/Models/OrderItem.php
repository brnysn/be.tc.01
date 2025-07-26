<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * OrderItem model
 *
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property int $quantity
 * @property float $unit_price
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \App\Models\Order $order
 * @property \App\Models\Product $product
 */
class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'unit_price' => 'float',
        'quantity' => 'integer',
    ];

    protected $appends = [
        'total_price',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            $item->unit_price = $item->product->price;
        });
    }

    /**
     * Relationships
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Helpers
     */
    public function calculateTotalPrice(): float
    {
        return $this->unit_price * $this->quantity;
    }
}
