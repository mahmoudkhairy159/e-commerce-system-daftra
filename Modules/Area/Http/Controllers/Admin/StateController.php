<?php

namespace Modules\Area\Http\Controllers\Admin;
use Exception;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Types\CacheKeysType;
use Illuminate\Support\Facades\Auth;
use Modules\Area\Repositories\StateRepository;
use Modules\Area\Transformers\Admin\State\StateCollection;
use Modules\Area\Transformers\Admin\State\StateResource;
use Modules\Area\Http\Requests\Admin\State\StoreStateRequest;
use Modules\Area\Http\Requests\Admin\State\UpdateStateRequest;

class StateController extends Controller
{
    use ApiResponseTrait;


    protected $stateRepository;

    protected $_config;
    protected $guard;

    public function __construct(StateRepository $stateRepository)
    {
        $this->guard = 'admin-api';
        request()->merge(['token' => 'true']);
        Auth::setDefaultDriver($this->guard);
        $this->_config = request('_config');
        $this->stateRepository = $stateRepository;
        // permissions
        $this->middleware('auth:' . $this->guard);
        $this->middleware(['permission:states.show'])->only(['index', 'show', 'getByCountryId']);
        $this->middleware(['permission:states.create'])->only(['store']);
        $this->middleware(['permission:states.update'])->only(['update']);
        $this->middleware(['permission:states.destroy'])->only(['destroy', 'forceDelete', 'restore', 'getOnlyTrashed']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = $this->stateRepository->getAll()->paginate();
            if (!$data) {
                return $this->messageResponse(
                    __("app.data_not_found'"),
                    false,
                    404
                );
            }
            return $this->successResponse(new StateCollection($data));
        } catch (Exception $e) {
            return $this->errorResponse(
                [$e->getMessage(), $e->getCode()],
                __('app.something-went-wrong'),
                500
            );
        }
    }


    public function getByCountryId($country_id)
    {
        try {
            $data = $this->stateRepository->getStatesByCountryId($country_id)->get();
            return $this->successResponse(StateResource::collection($data));
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStateRequest $request)
    {
        try {
            $data =  $request->validated();
            $data['created_by'] = auth()->guard($this->guard)->id();
            $created = $this->stateRepository->create($data);

            if ($created) {
                $this->clearStatesCache();
                $this->clearStatesByCountyIdCache($created->country_id);
                return $this->messageResponse(
                    __("area::app.states.created-successfully"),
                    true,
                    201
                );
            } {
                return $this->messageResponse(
                    __("area::app.states.created-failed"),
                    false,
                    400
                );
            }
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        try {
            $data = $this->stateRepository->findOrFail($id);
            return $this->successResponse(new StateResource($data));
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStateRequest $request, $id)
    {
        try {
            $state = $this->stateRepository->findOrFail($id);
            $data =  $request->validated();
            $data['updated_by'] = auth()->guard($this->guard)->id();
            $updated = $this->stateRepository->update($data, $id);

            if ($updated) {
                $this->clearStatesCache();
                $this->clearStatesByCountyIdCache($state->country_id);
                return $this->messageResponse(
                    __("area::app.states.updated-successfully"),
                    true,
                    200
                );
            } {
                return $this->messageResponse(
                    __("area::app.states.updated-failed"),
                    false,
                    400
                );
            }
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $state=$this->stateRepository->findOrFail($id);
            $deleted = $this->stateRepository->delete($id);
            if ($deleted) {
                $this->clearStatesCache();
                $this->clearStatesByCountyIdCache($state->country_id);
                return $this->messageResponse(
                    __("area::app.states.deleted-successfully"),
                    true,
                    200
                );
            } {
                return $this->messageResponse(
                    __("area::app.states.deleted-failed"),
                    false,
                    400
                );
            }
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }

    /***********Trashed model SoftDeletes**************/
    public function getOnlyTrashed()
    {
        try {
            $data = $this->stateRepository->getOnlyTrashed()->paginate();
            return $this->successResponse(new StateCollection($data));
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }

    public function forceDelete($id)
    {
        try {
            $deleted = $this->stateRepository->forceDelete($id);
            if ($deleted) {
                return $this->messageResponse(
                    __("area::app.states.deleted-successfully"),
                    true,
                    200
                );
            } {
                return $this->messageResponse(
                    __("area::app.states.deleted-failed"),
                    false,
                    400
                );
            }
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }

    public function restore($id)
    {
        try {
            $restored = $this->stateRepository->restore($id);
            if ($restored) {
                $this->clearStatesCache();
                $this->clearStatesByCountyIdCache($restored->country_id);
                return $this->messageResponse(
                    __("area::app.states.restored-successfully"),
                    true,
                    200
                );
            } {
                return $this->messageResponse(
                    __("area::app.states.restored-failed"),
                    false,
                    400
                );
            }
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }
    /***********Trashed model SoftDeletes**************/
    private function clearStatesCache()
    {
        $this->deleteCache(CacheKeysType::CITIES_CACHE);
    }
    private function clearStatesByCountyIdCache($country_id)
    {
        $this->deleteCache(CacheKeysType::statesCacheKey($country_id));
    }
}
