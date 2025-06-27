<?php

namespace Modules\Wishlist\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Models\User;

class Wishlist extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = ['user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wishlistProducts()
    {
        return $this->hasMany(WishlistProduct::class);
    }
}
