<?php

namespace Modules\Area\Repositories;

use App\Traits\SoftDeletableTrait;
use Modules\Area\Models\Country;
use Prettus\Repository\Eloquent\BaseRepository;

class CountryRepository extends BaseRepository
{
    use SoftDeletableTrait;

    public $retrievedData = [
        'countries.id',
        'code',
        'phone_code',
        'status',
    ];
    public function model()
    {
        return Country::class;
    }
    public function getAll()
    {
        return $this->model
            ->select($this->retrievedData)
            ->filter(request()->all())
            ->orderBy('created_at', 'asc');
    }
    public function getAllActive($locale)
    {

        return $this->model
        ->select($this->retrievedData)
        ->active()
        ->join('country_translations', 'country_translations.country_id', '=', 'countries.id') // Assuming you have a translations table
        ->where('country_translations.locale', $locale) // Filter by locale
        ->orderBy('countries.created_at', 'asc')
        ->get();
    }



}
