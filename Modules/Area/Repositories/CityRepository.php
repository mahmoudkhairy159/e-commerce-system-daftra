<?php

namespace Modules\Area\Repositories;

use App\Traits\SoftDeletableTrait;
use App\Traits\CacheTrait;
use App\Types\CacheKeysType;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Area\Models\City;
use Prettus\Repository\Eloquent\BaseRepository;

class CityRepository extends BaseRepository
{
    use SoftDeletableTrait;
    use CacheTrait;

    public $retrievedData = [
        'id',
        'state_id',
        'country_id',
        'longitude',
        'latitude',
        'status',
    ];

    public function model()
    {
        return City::class;
    }

    public function getAll()
    {
        return $this->model
            ->with('state', 'country')
            ->select($this->retrievedData)
            ->filter(request()->all())
            ->orderBy('created_at', 'asc');
    }

    /**
     * Get cached active cities by country ID with filter support
     */
    public function getCachedActiveCitiesByCountryId(int $countryId, string $locale = null)
    {
        // If filters are present, query database directly with filters
        if (!$this->shouldUseCache()) {
            return $this->getActiveCitiesByCountryIdFiltered($countryId);
        }

        return app('cache.cities')->getByCountry($countryId, $locale);
    }

    /**
     * Get cached active cities by state ID with filter support
     */
    public function getCachedActiveCitiesByStateId(int $stateId, string $locale = null)
    {
        // If filters are present, query database directly with filters
        if (!$this->shouldUseCache()) {
            return $this->getActiveCitiesByStateIdFiltered($stateId);
        }

        return app('cache.cities')->getByState($stateId, $locale);
    }

    /**
     * Get cached all cities with filter support
     */
    public function getCachedCities()
    {
        // If filters are present, query database directly with filters
        if (!$this->shouldUseCache()) {
            return $this->getAllFiltered();
        }

        return app('cache.cities')->getAll();
    }

    /*****************************************Filtered Query Methods ********************************************/

    /**
     * Get all cities with filters applied
     */
    private function getAllFiltered()
    {
        return $this->model
            ->with('state', 'country')
            ->filter(request()->all())
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get active cities by country ID with filters applied
     */
    private function getActiveCitiesByCountryIdFiltered(int $countryId)
    {
        return $this->model
            ->where('country_id', $countryId)
            ->active()
            ->filter(request()->all())
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get active cities by state ID with filters applied
     */
    private function getActiveCitiesByStateIdFiltered(int $stateId)
    {
        return $this->model
            ->where('state_id', $stateId)
            ->active()
            ->filter(request()->all())
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function getCitiesByCountryId($country_id)
    {
        return $this->model
            ->select($this->retrievedData)
            ->where('country_id', $country_id)
            ->filter(request()->all())
            ->orderBy('created_at', 'asc');
    }

    public function getCitiesByStateId($state_id)
    {
        return $this->model
            ->select($this->retrievedData)
            ->where('state_id', $state_id)
            ->filter(request()->all())
            ->orderBy('created_at', 'asc');
    }

    public function getActiveCitiesByCountryId($country_id)
    {
        return $this->model
            ->select($this->retrievedData)
            ->where('country_id', $country_id)
            ->active()
            ->filter(request()->all())
            ->orderBy('created_at', 'asc');
    }

    public function getActiveCitiesByStateId($state_id)
    {
        return $this->model
            ->select($this->retrievedData)
            ->where('state_id', $state_id)
            ->active()
            ->filter(request()->all())
            ->orderBy('created_at', 'asc');
    }

    /**
     * Create a new city
     */
    public function create(array $attributes)
    {
        try {
            DB::beginTransaction();

            $city = parent::create($attributes);

            // Invalidate related caches
            if (isset($attributes['country_id']) && isset($attributes['state_id'])) {
                app('cache.cities')->invalidate($attributes['country_id'], $attributes['state_id']);
            }

            DB::commit();
            return $city;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Update a city
     */
    public function update(array $attributes, $id)
    {
        try {
            DB::beginTransaction();

            $city = $this->find($id);
            $oldCountryId = $city->country_id;
            $oldStateId = $city->state_id;

            $updated = parent::update($attributes, $id);

            // Invalidate caches for old and new locations
            if (isset($attributes['country_id']) || isset($attributes['state_id'])) {
                $newCountryId = $attributes['country_id'] ?? $oldCountryId;
                $newStateId = $attributes['state_id'] ?? $oldStateId;

                // Invalidate old location cache
                app('cache.cities')->invalidate($oldCountryId, $oldStateId);

                // Invalidate new location cache if different
                if ($newCountryId !== $oldCountryId || $newStateId !== $oldStateId) {
                    app('cache.cities')->invalidate($newCountryId, $newStateId);
                }
            }

            DB::commit();
            return $updated;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Delete a city
     */
    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $city = $this->find($id);
            $deleted = parent::delete($id);

            // Invalidate related caches
            app('cache.cities')->invalidate($city->country_id, $city->state_id);

            DB::commit();
            return $deleted;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function restore(int $id)
    {
        try {
            DB::beginTransaction();
            $model = $this->model->withTrashed()->findOrFail($id);
            $restored = $model->restore();

            // Invalidate related caches
            app('cache.cities')->invalidate($model->country_id, $model->state_id);

            DB::commit();
            return $model;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }
}
