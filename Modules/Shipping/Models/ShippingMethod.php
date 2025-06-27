<?php

namespace Modules\Shipping\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use EloquentFilter\Filterable;
use Modules\Admin\Models\Admin;
use Modules\Shipping\Enums\ShippingMethodType;
use Modules\Shipping\Filters\ShippingMethodFilter;

class ShippingMethod extends Model implements TranslatableContract
{
    use HasFactory, Translatable, Filterable;

    protected $table = 'shipping_methods';

    protected $fillable = [
        'type',
        'flat_rate',
        'per_km_rate',
        'max_distance',
        'status'
    ];

    /**
     * The attributes that are translatable.
     *
     * @var array
     */
    public $translatedAttributes = [
        'title',
        'description'
    ];

    public function modelFilter()
    {
        return $this->provideFilter(ShippingMethodFilter::class);
    }



    protected $casts = [
        'flat_rate' => 'float',
        'per_km_rate' => 'float',
        'max_distance' => 'float'
    ];




    /**
     * Get the admin who created the product.
     */
    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    /**
     * Get the admin who updated the product.
     */
    public function updatedBy()
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }


   
}
