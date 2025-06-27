<?php

namespace Modules\Order\Models;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Order\Enums\OrderStatusEnum;
use Modules\Order\Filters\OrderProductFilter;
use Modules\Product\Models\Product;

class OrderProduct extends Model
{

    use HasFactory, Filterable;
    protected $fillable = [
        'order_id',
        'product_id',
        'original_price',
        'price',
        'discount_amount',
        'tax',
        'subtotal',
        'quantity',
        'status'
    ];


    public function modelFilter()
    {
        return $this->provideFilter(OrderProductFilter::class);
    }

    // Relationships

    /**
     * Get the order that owns the order product.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

   

    /**
     * Get the product details.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

}