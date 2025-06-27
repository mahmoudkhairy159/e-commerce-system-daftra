<?php

namespace Modules\Shipping\Http\Controllers\Admin;

use App\Traits\ApiResponseTrait;
use App\Traits\CacheTrait;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Shipping\Http\Requests\Admin\CreateShippingMethodRequest;
use Modules\Shipping\Http\Requests\Admin\UpdateShippingMethodRequest;
use Modules\Shipping\Repositories\ShippingMethodRepository;
use Modules\Shipping\Transformers\Admin\ShippingMethodResource;

class ShippingMethodController extends Controller
{
    use ApiResponseTrait;
    use CacheTrait;
    protected $shippingMethodRepository;
    protected $_config;
    protected $guard;

    public function __construct(ShippingMethodRepository $shippingMethodRepository)
    {
        $this->guard = 'admin-api';
        request()->merge(['token' => 'true']);
        Auth::setDefaultDriver($this->guard);
        $this->_config = request('_config');
        $this->shippingMethodRepository = $shippingMethodRepository;
        // permissions
        $this->middleware('auth:' . $this->guard);
        $this->middleware(['permission:shippingMethods.show'])->only(['index', 'show']);
        $this->middleware(['permission:shippingMethods.create'])->only(['store']);
        $this->middleware(['permission:shippingMethods.update'])->only(['update',]);
        $this->middleware(['permission:shippingMethods.destroy'])->only(['destroy']);
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


    public function store(CreateShippingMethodRequest $request)
    {
        try {
            $data =  $request->validated();
            $data['created_by'] = auth()->guard($this->guard)->id();
            $created = $this->shippingMethodRepository->create($data);


            if (!$created) {
                return $this->messageResponse(
                    __("shipping::app.shippingMethods.created-failed"),
                    false,
                    400
                );
            }

            return $this->messageResponse(
                __("shipping::app.shippingMethods.created-successfully"),
                true,
                201
            );
        } catch (Exception $e) {
            //    return  $this->messageResponse( $e->getMessage());
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
    public function update(UpdateShippingMethodRequest $request, $id)
    {
        try {
            $data =  $request->validated();
            $data['updated_by'] = auth()->guard($this->guard)->id();

            $updated = $this->shippingMethodRepository->update($data, $id);


            if (!$updated) {
                return $this->messageResponse(
                    __("shipping::app.shippingMethods.updated-failed"),
                    false,
                    400
                );
            }
            return $this->messageResponse(
                __("shipping::app.shippingMethods.updated-successfully"),
                true,
                200
            );
        } catch (Exception $e) {
            return $this->errorResponse(
                [$e->getMessage()],
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
            $deleted = $this->shippingMethodRepository->delete($id);


            if (!$deleted) {
                return $this->messageResponse(
                    __("shipping::app.shippingMethods.deleted-failed"),
                    false,
                    400
                );
            }

            return $this->messageResponse(
                __("shipping::app.shippingMethods.deleted-successfully"),
                true,
                200
            );
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }



}
