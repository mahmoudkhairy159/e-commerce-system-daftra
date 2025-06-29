<?php

namespace Modules\Category\Models;

use App\Traits\UploadFileTrait;
use Astrotomic\Translatable\Translatable;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Admin\Models\Admin;
use Modules\Category\Filters\CategoryFilter;
use Modules\Product\Models\Product;

class Category extends Model implements TranslatableContract
{
    use HasFactory, Translatable, Filterable, UploadFileTrait, SoftDeletes;

    protected $table = 'categories';

    /**
     * The attributes that are translatable.
     *
     * @var array
     */
    public $translatedAttributes = ['name', 'slug', 'description'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'image',
        'code',
        'created_by',
        'updated_by',
        'status',
        'position',
    ];

    /**
     * The attributes that should be appended to the model's array form.
     *
     * @var array
     */
    protected $appends = ['image_url'];

    /**
     * Get the URL of the image.
     *
     * @return string|null
     */
    protected function getImageUrlAttribute()
    {
        return $this->image ? $this->getFileAttribute($this->image) : null;
    }

    /**
     * Status constants for the event category.
     */
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    /************************************* Query Scopes **********************************************/

    /**
     * Scope a query to only include active event categories.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /************************************* End Query Scopes ******************************************/
    /**
     * Define the model filter for applying custom filters on queries.
     */
    public function modelFilter()
    {
        return $this->provideFilter(CategoryFilter::class);
    }

    /**
     * The associated table.
     *
     * @var string
     */

    /************************************* Relationships *********************************************/


    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_categories');
    }

    /**
     * Get the admin who created the category.
     */
    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    /**
     * Get the admin who updated the category.
     */
    public function updatedBy()
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }






    /*************************************End Relationships *********************************************/
}