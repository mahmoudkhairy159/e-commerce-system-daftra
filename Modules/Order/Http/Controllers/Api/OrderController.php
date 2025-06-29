<?php

namespace Modules\Order\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Cart\Repositories\CartRepository;
use Modules\Order\Enums\OrderPaymentMethodEnum;
use Modules\Order\Enums\OrderPaymentStatusEnum;
use Modules\Order\Http\Requests\Api\Order\StoreOrderWithValidationRequest;
use Modules\Order\Repositories\OrderRepository;
use Modules\Order\Services\OrderCalculationService;
use Modules\Order\Services\PurchaseOrderService;
use Modules\Order\Transformers\Api\Order\OrderCollection;
use Modules\Order\Transformers\Api\Order\OrderResource;

class OrderController extends Controller
{
    use ApiResponseTrait;

    protected $orderRepository;
    protected $cartRepository;

    protected $_config;
    protected $guard;

    public function __construct(OrderRepository $orderRepository, CartRepository $cartRepository, )
    {
        $this->guard = 'user-api';
        request()->merge(['token' => 'true']);
        Auth::setDefaultDriver($this->guard);
        $this->_config = request('_config');
        $this->orderRepository = $orderRepository;
        $this->cartRepository = $cartRepository;
        // permissions
        $this->middleware('auth:' . $this->guard);
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
            $userId = auth()->guard($this->guard)->id();
            $ownedOrders = $this->orderRepository->getByUserId($userId)->paginate();
            return $this->successResponse(new OrderCollection($ownedOrders));
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
    public function store(StoreOrderWithValidationRequest $request, PurchaseOrderService $purchaseOrderService, OrderCalculationService $orderCalculationService)
    {
        try {
            $data = $request->validated();
            $data['user_id'] = auth()->id();

            // Basic cart existence check (detailed validation is done in the service)
            $cart = $this->cartRepository->getCartByUserId($data['user_id']);
            if (!$cart || $cart->cartProducts->isEmpty()) {
                return $this->errorResponse([], __('validation.cart_empty'), 422);
            }

            $data['payment_method'] = OrderPaymentMethodEnum::CASH;
            $data['payment_status'] = OrderPaymentStatusEnum::PENDING;
            $transactionId = null;

            $created = $purchaseOrderService->purchaseOrder($data, transactionId: $transactionId, orderCalculationService: $orderCalculationService);

            if ($created) {
                return $this->successResponse([
                    'order' => $created,
                    'message' => __("order::app.orders.created-successfully")
                ], __("order::app.orders.created-successfully"), 201);
            } else {
                return $this->errorResponse(
                    [],
                    __("order::app.orders.created-failed"),
                    400
                );
            }
        } catch (Exception $e) {
            // Handle validation exceptions with detailed error information
            if ($e->getCode() === 422) {
                $errorData = [];

                // Check if there are nested validation errors
                if ($e->getPrevious() && $e->getPrevious()->getMessage()) {
                    $nestedErrors = json_decode($e->getPrevious()->getMessage(), true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $errorData['validation_details'] = $nestedErrors;
                    }
                }

                return $this->errorResponse(
                    $errorData,
                    $e->getMessage(),
                    422
                );
            }

            // Log unexpected errors for debugging
            Log::error('Order creation failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

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

            $data = $this->orderRepository->getOneForUser($id);
            if (!$data) {
                return $this->messageResponse(
                    __('app.data-not-found'),
                    false,
                    404
                );
            }
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
     * Cancel an order
     */
    public function destroy($id)
    {
        try {

            $deleted = $this->orderRepository->deleteOneByUser($id);

            if ($deleted) {
                return $this->messageResponse(
                    __('order::app.orders.cancelled-successfully'),
                    true,
                    200
                );
            }

            return $this->messageResponse(
                __('order::app.orders.cancellation-failed'),
                false,
                400
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