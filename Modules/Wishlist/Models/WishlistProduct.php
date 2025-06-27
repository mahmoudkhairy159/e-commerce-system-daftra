<?php

namespace Modules\Wishlist\Models;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Wishlist\Filters\WishlistProductFilter;
use Modules\Product\Models\Product;

class WishlistProduct extends Model
{
    use HasFactory,Filterable;

    protected $fillable = [
        'wishlist_id',
        'product_id',
    ];

    public function modelFilter()
    {
        return $this->provideFilter(WishlistProductFilter::class);
    }

    public function wishlist()
    {
        return $this->belongsTo(Wishlist::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
