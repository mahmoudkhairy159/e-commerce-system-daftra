<?php

namespace Modules\Area\Repositories;

use App\Traits\SoftDeletableTrait;
use App\Traits\CacheTrait;
use App\Types\CacheKeysType;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Area\Models\State;
use Prettus\Repository\Eloquent\BaseRepository;

class StateRepository extends BaseRepository
{
    use SoftDeletableTrait;
    use CacheTrait;

    public $retrievedData = [
        'id',
        'country_id',
        'code',
        'status',
    ];

    public function model()
    {
        return State::class;
    }

    public function getAll()
    {
        return $this->model
            ->with('country')
            ->select($this->retrievedData)
            ->filter(request()->all())
            ->orderBy('created_at', 'asc');
    }

    /**
     * Get cached active states by country ID with filter support
     */
    public function getCachedActiveStatesByCountryId(int $countryId, string $locale = null)
    {
        // If filters are present, query database directly with filters
        if (!$this->shouldUseCache()) {
            return $this->getActiveStatesByCountryIdFiltered($countryId);
        }

        return app('cache.states')->getByCountry($countryId, $locale);
    }

    /**
     * Get cached all states with filter support
     */
    public function getCachedStates()
    {
        // If filters are present, query database directly with filters
        if (!$this->shouldUseCache()) {
            return $this->getAllFiltered();
        }

        return app('cache.states')->getAll();
    }

    /*****************************************Filtered Query Methods ********************************************/

    /**
     * Get all states with filters applied
     */
    private function getAllFiltered()
    {
        return $this->model
            ->with('country')
            ->filter(request()->all())
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get active states by country ID with filters applied
     */
    private function getActiveStatesByCountryIdFiltered(int $countryId)
    {
        return $this->model
            ->where('country_id', $countryId)
            ->active()
            ->filter(request()->all())
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function getStatesByCountryId($country_id)
    {
        return $this->model
            ->select($this->retrievedData)
            ->where('country_id', $country_id)
            ->filter(request()->all())
            ->orderBy('created_at', 'asc');
    }

    public function getActiveStatesByCountryId($country_id)
    {
        return $this->model
            ->select($this->retrievedData)
            ->where('country_id', $country_id)
            ->active()
            ->filter(request()->all())
            ->orderBy('created_at', 'asc');
    }

    /**
     * Create a new state
     */
    public function create(array $attributes)
    {
        try {
            DB::beginTransaction();

            $state = parent::create($attributes);

            // Invalidate related caches
            if (isset($attributes['country_id'])) {
                app('cache.states')->invalidate($attributes['country_id']);
            }

            DB::commit();
            return $state;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Update a state
     */
    public function update(array $attributes, $id)
    {
        try {
            DB::beginTransaction();

            $state = $this->find($id);
            $oldCountryId = $state->country_id;

            $updated = parent::update($attributes, $id);

            // Invalidate caches for old and new country
            if (isset($attributes['country_id'])) {
                $newCountryId = $attributes['country_id'];

                // Invalidate old country cache
                app('cache.states')->invalidate($oldCountryId);

                // Invalidate new country cache if different
                if ($newCountryId !== $oldCountryId) {
                    app('cache.states')->invalidate($newCountryId);
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
     * Delete a state
     */
    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $state = $this->find($id);
            $deleted = parent::delete($id);

            // Invalidate related caches
            app('cache.states')->invalidate($state->country_id);

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
            app('cache.states')->invalidate($model->country_id);

            DB::commit();
            return $model;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }
}
