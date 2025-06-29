<?php

namespace Modules\Product\Models;

use App\Traits\ProductCalculationTrait;
use App\Traits\UploadFileTrait;
use Astrotomic\Translatable\Translatable;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Models\Admin;
use Modules\Cart\Models\CartProduct;
use Modules\Category\Models\Category;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderProduct;
use Modules\Product\Filters\ProductFilter;

class Product extends Model implements TranslatableContract
{
    use HasFactory, Translatable, Filterable, UploadFileTrait, SoftDeletes,ProductCalculationTrait;

    protected $table = 'products';
    const FILES_DIRECTORY = 'products';

    /**
     * The attributes that are translatable.
     *
     * @var array
     */
    public $translatedAttributes = [
        'name',
        'slug',
        'seo_description',
        'seo_keys',
        'short_description',
        'long_description',
        'additional',
        'return_policy',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'image',
        'video_url',
        'type',
        'created_by',
        'updated_by',
        'status',
        'position',
        'currency',
        'stock',
        'price',
        'offer_price',
        'tax_rate',
        'offer_start_date',
        'offer_end_date',
        'approval_status',
    ];

    /**
     * The attributes that should be appended to the model's array form.
     *
     * @var array
     */
    protected $appends = ['image_url', "is_wish_listed", "is_carted"];

    /**
     * Get the URL of the image.
     *
     * @return string|null
     */
    protected function getImageUrlAttribute()
    {
        return $this->image ? $this->getFileAttribute($this->image) : null;
    }

    const APPROVAL_STATUS_PENDING = 0;
    const APPROVAL_STATUS_APPROVED = 1;
    const APPROVAL_STATUS_REJECTED = 2;
    /**
     * Status constants for the event product.
     */
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    /************************************* Query Scopes **********************************************/

    /**
     * Scope a query to only include active event categories.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        // return $query->where('status', self::STATUS_ACTIVE)
        //     ->where('approval_status', self::APPROVAL_STATUS_APPROVED)
        //     ->where(function($query) {
        //         $query->whereNull('offer_end_date')
        //             ->orWhere('offer_end_date', '>', now());
        //     });

        return $query->where('status', self::STATUS_ACTIVE)
            ->where('approval_status', self::APPROVAL_STATUS_APPROVED);

    }

    /************************************* End Query Scopes ******************************************/


    public function modelFilter()
    {
        return $this->provideFilter(ProductFilter::class);
    }


    /************************************* Relationships *********************************************/

    public function getIsCartedAttribute()
    {
        return Auth::guard('user-api')->user() && Auth::guard('user-api')->user()->cart ? $this->cartProducts()->where("cart_id", Auth::guard('user-api')->user()->cart->id)->exists() : false;
    }

      

    /**
     * Get the admin who created the product.
     */
    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    /**
     * Get the admin who updated the product.
     */
    public function updatedBy()
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }

    public function productImages()
    {
        return $this->hasMany(ProductImage::class);
    }


    public function relatedProducts()
    {
        return $this->belongsToMany(Product::class, 'related_products', 'product_id', 'related_product_id');
    }

    public function accessories()
    {
        return $this->belongsToMany(Product::class, 'product_accessories', 'product_id', 'accessory_id');
    }
    public function productReviews()
    {
        return $this->hasMany(ProductReview::class);
    }


    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_products')
                    ->withPivot('price','tax', 'quantity','status')
                    ->withTimestamps();
    }

    public function cartProducts()
    {
        return $this->hasMany(CartProduct::class);
    }
    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }








    /*************************************End Relationships *********************************************/

}