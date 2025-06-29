<?php

namespace Modules\User\Models;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
   
use Modules\User\Filters\UserAddressFilter;

class UserAddress extends Model
{
    use HasFactory,Filterable;
    protected $fillable = [
        'user_id',
        'zip_code',
        'address',
        'type',
        'phone_code',
        'phone',
        'title',
        'longitude',
        'latitude',
        'created_by',
        'updated_by',
    ];

    public function modelFilter()
    {
        return $this->provideFilter(UserAddressFilter::class);
    }

    /**
     * Define the relationship to the User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    //append_attribute is_default
    protected $appends = ['is_default'];
    public function getIsDefaultAttribute()
    {
        return $this->id === $this->user->default_address_id;
    }
}