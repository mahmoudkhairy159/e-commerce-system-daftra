<?php

namespace Modules\Area\Models;

use Astrotomic\Translatable\Translatable;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Admin\Models\Admin;
use Modules\Area\Filters\CountryFilter;

class Country extends Model implements TranslatableContract
{
    use HasFactory, Translatable, Filterable, SoftDeletes;

    protected $table = 'countries';


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
        'phone_code',
        'longitude',
        'latitude',
        'geometry',
        'status',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'geometry' => 'array',
    ];

    // Status constants
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /************************************* Query Scopes ***************************************************/

    /**
     * Scope a query to only include active countries.
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
        return $this->provideFilter(CountryFilter::class);
    }

    /********************************** End Filterable ****************************************************/

    /********************************** Relationships *****************************************************/

    /**
     * Get the states associated with the country.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function states()
    {
        return $this->hasMany(State::class);
    }

    /**
     * Get the cities associated with the country.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cities()
    {
        return $this->hasMany(City::class);
    }

    // /**
    //  * Get the users associated with the country.
    //  *
    //  * @return \Illuminate\Database\Eloquent\Relations\HasMany
    //  */
    // public function users()
    // {
    //     return $this->hasMany(User::class, 'country_id');
    // }

    /**
     * Get the admin who created the country.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    /**
     * Get the admin who updated the country.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy()
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }



    /********************************** End Relationships *************************************************/

}
