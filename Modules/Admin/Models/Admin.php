<?php

namespace Modules\Admin\Models;

use App\Traits\UploadFileTrait;
use EloquentFilter\Filterable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Modules\Admin\Filters\AdminFilter;
use Modules\Order\Models\OrderStatusHistory;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, Filterable, UploadFileTrait, HasApiTokens;


    protected $table = 'admins';

    const FILES_DIRECTORY = 'admins';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'image',
        'password',
        'role_id',
        'status',
        'blocked',
        'password_updated_at',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Status constants
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /********************************** Filterable ***********************************************/

    public function modelFilter()
    {
        return $this->provideFilter(AdminFilter::class);
    }

    /********************************** End Filterable *******************************************/

    /********************************** Image Handling *******************************************/

    /**
     * The attributes that should be appended to the model's array form.
     *
     * @var array
     */
    protected $appends = ['image_url'];

    /**
     * Accessor for the image URL.
     *
     * @return string|null
     */
    protected function getImageUrlAttribute()
    {
        return $this->image ? $this->getFileAttribute($this->image) : null;
    }

    /********************************** End Image Handling **************************************/

    /********************************** Mutators *************************************************/

    /**
     * Set the password attribute with hashing.
     *
     * @param string $value
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /********************************** End Mutators *********************************************/



    /********************************** Relationships ********************************************/

    /**
     * Get the role associated with the admin.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }


    /**
     * Get permissions associated with the admin's role.
     *
     * @return \Illuminate\Support\Collection
     */
    public function permissions()
    {
        return collect($this->role->permissions);
    }

    /**
     * Check if the admin has a specific permission.
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission($permission)
    {

        if ($this->role->permission_type == Role::PERMISSION_TYPE_ALL) {
            return true;
        }

        if ($this->role->permission_type == Role::PERMISSION_TYPE_CUSTOM && !$this->role->permissions) {
            return false;
        }

        return in_array($permission, $this->role->permissions);
    }
    /**
     * Get all of the order status changes by this user.
     */
    public function orderStatusChanges()
    {
        return $this->morphMany(OrderStatusHistory::class, 'changer');
    }

}
