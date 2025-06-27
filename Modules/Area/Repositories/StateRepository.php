<?php

namespace Modules\Area\Repositories;

use App\Traits\SoftDeletableTrait;
use App\Types\CacheKeysType;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Area\Models\State;
use Prettus\Repository\Eloquent\BaseRepository;

class StateRepository extends BaseRepository
{
    use SoftDeletableTrait;

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


    public function getCachedActiveStatesByCountryId($country_id)
    {
        return Cache::remember(CacheKeysType::statesCacheKey($country_id), now()->addDays(5), function () use ($country_id) {
            return $this->getActiveStatesByCountryId($country_id);
        });
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

    public function restore(int $id)
    {
        try {
            DB::beginTransaction();
            $model = $this->model->withTrashed()->findOrFail($id);
            $restored = $model->restore();
            DB::commit();
            return  $model;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }
}