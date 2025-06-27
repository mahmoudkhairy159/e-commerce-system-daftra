<?php

namespace Modules\Shipping\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use App\Traits\CacheTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Modules\Shipping\Transformers\Admin\ShippingMethodResource;
use Modules\Shipping\Repositories\ShippingMethodRepository;

class ShippingMethodController extends Controller
{
    use ApiResponseTrait;
    use CacheTrait;
    protected $shippingMethodRepository;
    protected $_config;
    protected $guard;

    public function __construct(ShippingMethodRepository $shippingMethodRepository)
    {
        $this->guard = 'user-api';
        request()->merge(['token' => 'true']);
        Auth::setDefaultDriver($this->guard);
        $this->_config = request('_config');
        $this->shippingMethodRepository = $shippingMethodRepository;

    }

    /**
     * Display a listing of the shipping methods.
     */
    public function index()
    {
        try {
            $data = $this->shippingMethodRepository->filter(request()->all())->orderBy('id', 'desc')->get();
            return $this->successResponse(ShippingMethodResource::collection($data));
        } catch (Exception $e) {
            return $this->errorResponse([], __('app.something-went-wrong'), 500);
        }
    }


    //   Display the specified resource.
    public function show($id)
    {
        try {
            $data = $this->shippingMethodRepository->findOrFail($id);
            return $this->successResponse(new ShippingMethodResource($data));
        } catch (Exception $e) {
            dd($e->getMessage());

            return $this->errorResponse([], __('app.something-went-wrong'), 500);
        }
    }


  
}