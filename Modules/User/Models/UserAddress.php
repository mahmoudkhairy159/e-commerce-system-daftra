<?php

namespace Modules\User\Models;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Area\Models\City;
use Modules\Area\Models\Country;
use Modules\Area\Models\State;
use Modules\User\Filters\UserAddressFilter;

class UserAddress extends Model
{
    use HasFactory,Filterable;
    protected $fillable = [
        'user_id',
        'country_id',
        'state_id',
        'city_id',
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

    /**
     * Define the relationship to the Country.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Define the relationship to the State.
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Define the relationship to the City.
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }
    //append_attribute is_default
    protected $appends = ['is_default'];
    public function getIsDefaultAttribute()
    {
        return $this->id === $this->user->default_address_id;
    }
}