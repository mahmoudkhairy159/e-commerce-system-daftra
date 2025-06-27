<?php

namespace Modules\Order\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Modules\Order\Http\Requests\Admin\OrderProduct\UpdateOrderProductRequest;
use Modules\Order\Http\Requests\Admin\OrderProduct\UpdateOrderProductStatusRequest;
use Modules\Order\Repositories\OrderProductRepository;
use Modules\Order\Transformers\Admin\OrderProduct\OrderProductCollection;
use Modules\Order\Transformers\Admin\OrderProduct\OrderProductResource;

class OrderProductController extends Controller
{
    use ApiResponseTrait;

    protected $orderProductRepository;

    protected $_config;
    protected $guard;

    public function __construct(OrderProductRepository $orderProductRepository)
    {
        $this->guard = 'admin-api';
        request()->merge(['token' => 'true']);
        Auth::setDefaultDriver($this->guard);
        $this->_config = request('_config');
        $this->orderProductRepository = $orderProductRepository;
        // permissions
        $this->middleware('auth:' . $this->guard);
        $this->middleware(['permission:orders.show'])->only(['getByOrderId']);
    }
    /**Introduction
    Issues
    Changelog
    FAQ

     * Display a listing of the resource.
     */
    public function getByOrderId($orderId)
    {
        try {
            $data = $this->orderProductRepository->getByOrderId($orderId)->paginate();
            return $this->successResponse(new OrderProductCollection($data));
        } catch (Exception $e) {
            return $this->errorResponse(
                [$e->getMessage(), $e->getCode()],
                __('app.something-went-wrong'),
                500
            );
        }
    }





    



}
