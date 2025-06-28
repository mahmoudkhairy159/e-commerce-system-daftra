<?php

namespace Modules\Area\Http\Controllers\Api;

use Exception;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Types\CacheKeysType;
use Illuminate\Support\Facades\Auth;
use Modules\Area\Repositories\CountryRepository;
use Modules\Area\Transformers\Api\Country\CountryResource;


class CountryController extends Controller
{
    use ApiResponseTrait;


    protected $countryRepository;

    protected $_config;
    protected $guard;

    public function __construct(CountryRepository $countryRepository)
    {


        $this->guard = 'user-api';
        request()->merge(['token' => 'true']);
        Auth::setDefaultDriver($this->guard);
        $this->_config = request('_config');
        $this->countryRepository = $countryRepository;
        // permissions
        // $this->middleware('auth:' . $this->guard);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $currentLocale = core()->getCurrentLocale();
            $data = $this->countryRepository->getCachedActiveCountries($currentLocale);

            if (!$data || $data->isEmpty()) {
                return $this->messageResponse(
                    __("app.data_not_found"),
                    false,
                    404
                );
            }

            return $this->successResponse(CountryResource::collection($data));
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



}
