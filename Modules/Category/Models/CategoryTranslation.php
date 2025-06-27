<?php

namespace Modules\Category\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoryTranslation extends Model
{
    use HasFactory,Sluggable;

    protected $fillable = ['name','slug', 'description'];

    protected $table = 'category_translations';

    public $timestamps = false;
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
}
