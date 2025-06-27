<?php

namespace Modules\Order\Models;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Order\Filters\OrderFilter;
use Modules\User\Models\User;

class Order extends Model
{
    use HasFactory, Filterable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'order_number',
        'user_id',
        'total_amount',
        'tax_amount',
        'shipping_amount',
        'discount_amount',
        'status',
        'payment_status',
        'payment_method',
        'order_address',
        'shipping_method',
        'notes',
    ];
    // Cast attributes
    protected $casts = [
        'order_address' => 'array',
        'shipping_method' => 'array',
    ];
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($order) {
            $order->order_number = $order->generateOrderNumber();
        });
    }

    /**
     * Generate a unique order number
     *
     * @return string
     */
    public function generateOrderNumber()
    {
        // Get current date components
        $year = date('Y');
        $month = date('m');
        $day = date('d');

        // Get the latest order to determine the sequence
        $latestOrder = Order::orderBy('id', 'desc')->first();

        // Start sequence from 1 if no orders exist
        $sequence = $latestOrder ? intval(substr($latestOrder->order_number, -4)) + 1 : 1;

        // Format sequence to always be 4 digits with leading zeros
        $sequenceFormatted = str_pad($sequence, 4, '0', STR_PAD_LEFT);

        // Format: OR-YYYYMMDD-XXXX (OR prefix, date, and 4-digit sequence)
        return "OR-{$year}{$month}{$day}-{$sequenceFormatted}";
    }


    public function modelFilter()
    {
        return $this->provideFilter(OrderFilter::class);
    }









    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }


  

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }
    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }


}