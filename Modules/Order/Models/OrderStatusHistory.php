<?php

namespace Modules\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\User\Models\User;

class OrderStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'order_product_id',
        'changer_type',   // This will store the class name of the model (Admin, User)
        'changer_id',     // This will store the ID of the model
        'status_from',
        'status_to',
        'comment',
    ];
    /**
     * Get the parent changer model (User, Admin).
     */
    public function changer()
    {
        return $this->morphTo();
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderProduct()
    {
        return $this->belongsTo(OrderProduct::class);
    }
    //?
    public function user()
    {
        return $this->belongsTo(User::class);
    }


}