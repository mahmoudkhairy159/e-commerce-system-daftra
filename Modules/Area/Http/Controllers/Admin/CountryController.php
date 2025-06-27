<?php

namespace Modules\Area\Http\Controllers\Admin;

use Exception;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Types\CacheKeysType;
use Illuminate\Support\Facades\Auth;
use Modules\Area\Repositories\CountryRepository;
use Modules\Area\Transformers\Admin\Country\CountryResource;
use Modules\Area\Transformers\Admin\Country\CountryCollection;
use Modules\Area\Http\Requests\Admin\Country\StoreCountryRequest;
use Modules\Area\Http\Requests\Admin\Country\UpdateCountryRequest;

class CountryController extends Controller
{
    use ApiResponseTrait;
    protected $countryRepository;
    protected $_config;
    protected $guard;
    public function __construct(CountryRepository $countryRepository)
    {
        $this->guard = 'admin-api';
        request()->merge(['token' => 'true']);
        Auth::setDefaultDriver($this->guard);
        $this->_config = request('_config');
        $this->countryRepository = $countryRepository;
        // permissions
        $this->middleware('auth:' . $this->guard);
        $this->middleware(['permission:countries.show'])->only(['index', 'show']);
        $this->middleware(['permission:countries.create'])->only(['store']);
        $this->middleware(['permission:countries.update'])->only(['update']);
        $this->middleware(['permission:countries.destroy'])->only(['destroy', 'forceDelete', 'restore', 'getOnlyTrashed']);
    }
    /**Introduction
    Issues
    Changelog
    FAQ

     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = $this->countryRepository->getAll()->paginate();
            return $this->successResponse(new CountryCollection($data));
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
    public function store(StoreCountryRequest $request)
    {
        try {
            $data =  $request->validated();
            $data['created_by'] = auth()->guard($this->guard)->id();
            $created = $this->countryRepository->create($data);

            if ($created) {
                $this->clearCountriesCache();
                return $this->messageResponse(
                    __("area::app.countries.created-successfully"),
                    true,
                    201
                );
            } {
                return $this->messageResponse(
                    __("area::app.countries.created-failed"),
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
            $data = $this->countryRepository->findOrFail($id);
            return $this->successResponse(new CountryResource($data));
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
    public function update(UpdateCountryRequest $request, $id)
    {
        try {
            $country = $this->countryRepository->findOrFail($id);
            $data =  $request->validated();
            $data['updated_by'] = auth()->guard($this->guard)->id();
            $updated = $this->countryRepository->update($data, $id);

            if ($updated) {
                $this->clearCountriesCache();
                return $this->messageResponse(
                    __("area::app.countries.updated-successfully"),
                    true,
                    200
                );
            } {
                return $this->messageResponse(
                    __("area::app.countries.updated-failed"),
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
            $deleted = $this->countryRepository->delete($id);
            if ($deleted) {
                $this->clearCountriesCache();
                return $this->messageResponse(
                    __("area::app.countries.deleted-successfully"),
                    true,
                    200
                );
            } {
                return $this->messageResponse(
                    __("area::app.countries.deleted-failed"),
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
            $data = $this->countryRepository->getOnlyTrashed()->get();
            return $this->successResponse(CountryResource::collection($data));
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
            $deleted = $this->countryRepository->forceDelete($id);
            if ($deleted) {
                return $this->messageResponse(
                    __("area::app.countries.deleted-successfully"),
                    true,
                    200
                );
            } {
                return $this->messageResponse(
                    __("area::app.countries.deleted-failed"),
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
            $restored = $this->countryRepository->restore($id);
            if ($restored) {
                $this->clearCountriesCache();
                return $this->messageResponse(
                    __("area::app.countries.restored-successfully"),
                    true,
                    200
                );
            } {
                return $this->messageResponse(
                    __("area::app.countries.restored-failed"),
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
    private function clearCountriesCache()
    {
        $this->deleteCache(CacheKeysType::CITIES_CACHE);
    }
}
