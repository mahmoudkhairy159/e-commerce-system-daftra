<?php

namespace Modules\User\Models;

use App\Traits\UploadFileTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use EloquentFilter\Filterable;

class UserProfile extends Model
{
    use HasFactory;
    use Filterable;
    use UploadFileTrait;




    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bio',
        'language',
        'mode',
        'sound_effects',
        'gender',
        'birth_date',
        'user_id'
    ];

    public $timestamps = false;


    /**
     * Get the admins.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
