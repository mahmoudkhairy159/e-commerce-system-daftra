<?php

namespace Modules\Shipping\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingMethodTranslation extends Model
{
    use HasFactory;

    protected $table = 'shipping_method_translations';

    protected $fillable = [
        'shipping_method_id',
        'locale',
        'title',
        'description'
    ];


    public $timestamps = false;

    public function shippingMethod(): BelongsTo
    {
        return $this->belongsTo(ShippingMethod::class);
    }
}
