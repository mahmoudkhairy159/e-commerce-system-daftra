<?php

namespace Modules\Cart\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Modules\Cart\Http\Requests\Api\Cart\AddToCartRequest;
use Modules\Cart\Http\Requests\Api\Cart\UpdateProductCartRequest;
use Modules\Cart\Transformers\Api\CartProduct\CartProductResource;
use Modules\Cart\Repositories\CartRepository;

class CartController extends Controller
{
    use ApiResponseTrait;
    protected $cartRepository;
    protected $_config;
    protected $guard;
    public function __construct(CartRepository $cartRepository)
    {
        $this->guard = 'user-api';
        request()->merge(['token' => 'true']);
        Auth::setDefaultDriver($this->guard);
        $this->_config = request('_config');
        $this->cartRepository = $cartRepository;
        // permissions
        $this->middleware('auth:' . $this->guard);
    }

    public function viewCart()
    {
        try {
            $userId = Auth::guard('user-api')->id();

            // Get all cart products
            $cartProducts = $this->cartRepository->getProducts($userId)->get();

            // Get cart totals using the updated getCartSumTotal method
            $cartTotals = $this->cartRepository->getCartSumTotal($userId);

            $data = [
                'cartProducts' => CartProductResource::collection($cartProducts),
                // Include all totals from the getCartSumTotal method
                'price_amount' => $cartTotals['price_amount'],
                'original_price_amount' => $cartTotals['original_price_amount'],
                'sum_tax' => $cartTotals['tax_amount'],
                'sum_quantity' => $cartTotals['sum_quantity'],
                'sum_discount_amount' => $cartTotals['sum_discount_amount'],
                'sum_subtotal' => $cartTotals['sum_subtotal'],
            ];
            return $this->successResponse($data);
        } catch (Exception $e) {

            return $this->errorResponse(
                [$e->getMessage()],
                __('app.something-went-wrong'),
                500
            );
        }
    }
    public function addToCart(AddToCartRequest $request)
    {
        try {
            $data = $request->validated();
            $added = $this->cartRepository->addProduct($data);

            if ($added) {
                return $this->messageResponse(
                    __("cart::app.cartProducts.created-successfully"),
                    true,
                    201
                );
            }{
                return $this->messageResponse(
                    __("cart::app.cartProducts.created-failed"),
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

    public function removeFromCart($id)
    {
        try {
            $removed = $this->cartRepository->removeProduct($id);
            if ($removed) {
                return $this->messageResponse(
                    __("cart::app.cartProducts.deleted-successfully"),
                    true,
                    201
                );
            }{
                return $this->messageResponse(
                    __("cart::app.cartProducts.deleted-failed"),
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

    public function updateProductCart(UpdateProductCartRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $updated = $this->cartRepository->updateProductCart($id, $data);
            if ($updated) {
                return $this->messageResponse(
                    __("cart::app.cartProducts.updated-successfully"),
                    true,
                    201
                );
            }{
                return $this->messageResponse(
                    __("cart::app.cartProducts.updated-failed"),
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
    public function clearCart()
    {
        try {
            $cartId=auth()->user()->cart->id;
            $removed = $this->cartRepository->emptyCart($cartId);
            if ($removed) {
                return $this->messageResponse(
                    __("cart::app.cart.cleared-successfully"),
                    true,
                    201
                );
            }{
                return $this->messageResponse(
                    __("cart::app.cart.cleared-failed"),
                    false,
                    400
                );
            }
        } catch (Exception $e) {
            dd($e->getMessage());
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }
}
