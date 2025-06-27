<?php

namespace Modules\Area\Models;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Admin\Models\Admin;
use Modules\Area\Filters\StateFilter;

class State extends Model implements TranslatableContract
{
    use HasFactory, Filterable, Translatable, SoftDeletes;

    protected $table = 'states';


    /**
     * The attributes that can be translated.
     *
     * @var array
     */
    public $translatedAttributes = ['name'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'longitude',
        'latitude',
        'status',
        'created_by',
        'updated_by',
        'country_id',
    ];

    // Status constants
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /************************************* Query Scopes ***************************************************/

    /**
     * Scope a query to only include active states.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /************************************* End Query Scopes ***********************************************/

    /********************************** Filterable ********************************************************/


    public function modelFilter()
    {
        return $this->provideFilter(StateFilter::class);
    }

    /********************************** End Filterable ****************************************************/

    /********************************** Relationships *****************************************************/

    /**
     * Get the cities associated with the state.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cities()
    {
        return $this->hasMany(City::class);
    }

    /**
     * Get the country associated with the state.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    /**
     * Get the admin who created the state.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    /**
     * Get the admin who updated the state.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy()
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }

    /**
     * Get the users associated with the state through cities.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    // public function users()
    // {
    //     return $this->hasManyThrough(
    //         User::class,     // Final destination model
    //         City::class,     // Intermediate model
    //         'state_id',      // Foreign key on cities table
    //         'city_id',       // Foreign key on users table
    //         'id',            // Local key on states table
    //         'id'             // Local key on cities table
    //     );
    // }





    /********************************** End Relationships *************************************************/

}
