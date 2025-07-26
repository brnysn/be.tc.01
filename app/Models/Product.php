<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'sku',
        'price',
        'stock_quantity',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'price' => 'float',
        'stock_quantity' => 'integer',
    ];

    public static $sortable = [
        'name',
        'price',
        'stock_quantity',
        'created_at',
        'updated_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($product) {
            Cache::tags('products')->flush();
        });

        static::updated(function ($product) {
            Cache::tags('products')->flush();
        });

        static::deleted(function ($product) {
            Cache::tags('products')->flush();
        });
    }

    /**
     * Helpers
     */
    public function isInStock(int $quantity = 1): bool
    {
        return $this->stock_quantity >= $quantity;
    }

    public function decreaseStockQuantity(int $quantity = 1): void
    {
        $this->stock_quantity -= $quantity;
        $this->save();
    }

    public function increaseStockQuantity(int $quantity = 1): void
    {
        $this->stock_quantity += $quantity;
        $this->save();
    }
}
