<?php

namespace Modules\Area\Repositories;

use App\Traits\SoftDeletableTrait;
use App\Traits\CacheTrait;
use Illuminate\Support\Facades\DB;
use Modules\Area\Models\Country;
use Prettus\Repository\Eloquent\BaseRepository;

class CountryRepository extends BaseRepository
{
    use SoftDeletableTrait;
    use CacheTrait;

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
            ->join('country_translations', 'country_translations.country_id', '=', 'countries.id')
            ->where('country_translations.locale', $locale)
            ->orderBy('countries.created_at', 'asc')
            ->get();
    }

    /**
     * Get cached active countries by locale with filter support
     */
    public function getCachedActiveCountries(string $locale)
    {
        // If filters are present, query database directly with filters
        if (!$this->shouldUseCache()) {
            return $this->getAllActiveFiltered($locale);
        }

        return app('cache.countries')->get($locale);
    }

    /**
     * Get cached active countries for all locales with filter support
     */
    public function getCachedActiveCountriesAll()
    {
        // If filters are present, query database directly with filters
        if (!$this->shouldUseCache()) {
            return $this->getAllFiltered();
        }

        return app('cache.countries')->getAll();
    }

    /*****************************************Filtered Query Methods ********************************************/

    /**
     * Get all countries with filters applied
     */
    private function getAllFiltered()
    {
        return $this->model
            ->filter(request()->all())
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get active countries with filters applied
     */
    private function getAllActiveFiltered(string $locale)
    {
        return $this->model
            ->active()
            ->join('country_translations', 'country_translations.country_id', '=', 'countries.id')
            ->where('country_translations.locale', $locale)
            ->filter(request()->all())
            ->orderBy('countries.created_at', 'asc')
            ->get();
    }

    /**
     * Create a new country
     */
    public function create(array $attributes)
    {
        try {
            DB::beginTransaction();

            $country = parent::create($attributes);

            // Invalidate countries cache
            app('cache.countries')->invalidate();

            DB::commit();
            return $country;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Update a country
     */
    public function update(array $attributes, $id)
    {
        try {
            DB::beginTransaction();

            $updated = parent::update($attributes, $id);

            // Invalidate countries cache
            app('cache.countries')->invalidate();

            DB::commit();
            return $updated;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Delete a country
     */
    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $deleted = parent::delete($id);

            // Invalidate countries cache
            app('cache.countries')->invalidate();

            DB::commit();
            return $deleted;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Restore a country
     */
    public function restore(int $id)
    {
        try {
            DB::beginTransaction();
            $model = $this->model->withTrashed()->findOrFail($id);
            $restored = $model->restore();

            // Invalidate countries cache
            app('cache.countries')->invalidate();

            DB::commit();
            return $model;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }
}
