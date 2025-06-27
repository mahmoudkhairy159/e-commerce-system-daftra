<?php

namespace Modules\User\Models;


use App\Traits\UploadFileTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use EloquentFilter\Filterable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Modules\Cart\Models\Cart;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderStatusHistory;
use Modules\User\Filters\UserFilter;
use Modules\Wishlist\Models\Wishlist;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use HasFactory;
    use Notifiable;
    use Filterable;
    use UploadFileTrait;
    use Sluggable;

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];
    //status
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    //status
//active
    const ACTIVE = 1;
    const INACTIVE = 0;
    //active
    public function modelFilter()
    {
        return $this->provideFilter(UserFilter::class);
    }

    //slug
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['name', 'id'],
                'separator' => '-',
            ],
        ];
    }
    //slug
    const FILES_DIRECTORY = '/users';

    protected $appends = ['image_url'];
    //image
    protected function getImageUrlAttribute()
    {
        return $this->image ? $this->getFileAttribute($this->image) : null;
    }
    //image
    //Mutators
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }
    //Mutators

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)->where('active', self::STATUS_ACTIVE);
    }

    // /*******************relationships********************/
    public function linkedSocialAccounts()
    {
        return $this->hasOne(LinkedSocialAccount::class);
    }
    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id');
    }
    public function cart()
    {
        return $this->hasOne(Cart::class);
    }
    public function wishlist()
    {
        return $this->hasOne(Wishlist::class);
    }
    public function userAddresses()
    {
        return $this->hasMany(UserAddress::class);
    }
    public function defaultAddress()
    {
        return $this->belongsTo(UserAddress::class, 'default_address_id');
    }
    // OTP
    public function otps()
    {
        return $this->hasMany(UserOTP::class, 'user_id');
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
      /**
     * Get all of the order status changes by this user.
     */
    public function orderStatusChanges()
    {
        return $this->morphMany(OrderStatusHistory::class, 'changer');
    }

}