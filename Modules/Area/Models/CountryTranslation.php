<?php

namespace Modules\Area\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CountryTranslation extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
    protected $table = 'country_translations';



    public $timestamps = false;
}
