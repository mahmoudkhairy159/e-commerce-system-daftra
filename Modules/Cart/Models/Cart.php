<?php

namespace Modules\Cart\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Models\User;

class Cart extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = ['user_id'];

    public function cartProducts()
    {
        return $this->hasMany(CartProduct::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
