<?php

namespace Modules\Cart\Models;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Cart\Filters\CartProductFilter;
use Modules\Product\Models\Product;

class CartProduct extends Model
{
    use HasFactory,Filterable;

    protected $fillable = [
        'cart_id',
        'product_id',
        'name' ,
        'quantity',
        'original_price',
        'price',
        'discount_amount',
        'tax',
        'subtotal',
        'options',
        'expires_at'
    ];
    protected $casts = [
        'options' => 'array',
    ];
    public function modelFilter()
    {
        return $this->provideFilter(CartProductFilter::class);
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}