<?php

namespace Modules\Order\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Modules\Order\Http\Requests\Admin\Order\UpdateOrderStatusRequest;
use Modules\Order\Repositories\OrderRepository;
use Modules\Order\Transformers\Admin\Order\OrderCollection;
use Modules\Order\Transformers\Admin\Order\OrderResource;

class OrderController extends Controller
{
    use ApiResponseTrait;

    protected $orderRepository;

    protected $_config;
    protected $guard;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->guard = 'admin-api';
        request()->merge(['token' => 'true']);
        Auth::setDefaultDriver($this->guard);
        $this->_config = request('_config');
        $this->orderRepository = $orderRepository;
       // permissions
       $this->middleware('auth:' . $this->guard);
       $this->middleware(['permission:orders.show'])->only(['index','getAllByStatus','getByUserId', 'show']);
       $this->middleware(['permission:orders.update'])->only(['updateStatus']);
       $this->middleware(['permission:orders.destroy'])->only(['destroy']);
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
            $data = $this->orderRepository->getAll()->paginate();
            return $this->successResponse(new OrderCollection($data));
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }


    public function getAllByStatus($status)
    {
        try {
            $data = $this->orderRepository->getAllByStatus($status)->paginate();
            return $this->successResponse(new OrderCollection($data));
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }
    public function getByUserId($user_id)
    {
        try {
            $data = $this->orderRepository->getByUserId($user_id)->paginate();
            return $this->successResponse(new OrderCollection($data));
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
            $data = $this->orderRepository-> getOneById($id);
            return $this->successResponse(new OrderResource($data));
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

            $deleted = $this->orderRepository->deleteOne($id);

            if ($deleted) {
                return $this->messageResponse(
                    __('order::app.orders.deleted-successfully'),
                    true,
                    200
                );
            }{
                return $this->messageResponse(
                    __('order::app.orders.deleted-failed'),
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
    // Change the status of an order
    public function updateStatus(UpdateOrderStatusRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $updated = $this->orderRepository->updateStatus($data, $id);

            if ($updated) {
                return $this->successResponse(
                    new OrderResource($updated),
                    __('order::app.orders.updated-successfully'),
                    200
                );
            }{
                return $this->messageResponse(
                    __('order::app.orders.updated-failed'),
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

}
