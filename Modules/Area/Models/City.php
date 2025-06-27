<?php

namespace Modules\Area\Models;

use Astrotomic\Translatable\Translatable;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Modules\Admin\Models\Admin;
use Modules\Area\Filters\CityFilter;
use Modules\User\Models\User;

class City extends Model implements TranslatableContract
{
    use HasFactory, Filterable, Translatable, SoftDeletes;
    protected $table = 'cities';

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
        'longitude',
        'latitude',
        'status',
        'created_by',
        'updated_by',
        'country_id',
        'state_id',
    ];

    // Status constants
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /************************************* Query Scopes ***************************************************/

    /**
     * Scope a query to only include active cities.
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
        return $this->provideFilter(CityFilter::class);
    }

    /********************************** End Filterable ****************************************************/

    /********************************** Relationships *****************************************************/

    /**
     * Get the country that the city belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    /**
     * Get the state that the city belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    /**
     * Get the admin who created the city.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    /**
     * Get the admin who updated the city.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy()
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }

    /**
     * Get the users associated with the city.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class, 'city_id');
    }

    /********************************** End Relationships *************************************************/

}
