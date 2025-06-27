<?php

namespace Modules\Product\Models;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Product\Filters\ProductReviewFilter;
use Modules\User\Models\User;

class ProductReview extends Model
{
    use HasFactory,Filterable;
    protected $fillable = ['product_id','user_id','comment', 'rating','status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function modelFilter()
    {
        return $this->provideFilter(ProductReviewFilter::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
