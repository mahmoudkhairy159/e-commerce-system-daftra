<?php

namespace Modules\Cart\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Modules\Cart\Transformers\Admin\CartProduct\CartProductCollection;
use Modules\Cart\Repositories\CartRepository;

class CartController extends Controller
{
    use ApiResponseTrait;
    protected $cartRepository;
    protected $_config;
    protected $guard;
    public function __construct(CartRepository $cartRepository)
    {
        $this->guard = 'admin-api';
        request()->merge(['token' => 'true']);
        Auth::setDefaultDriver($this->guard);
        $this->_config = request('_config');
        $this->cartRepository = $cartRepository;
        // permissions
        $this->middleware('auth:' . $this->guard);
        $this->middleware(['permission:cart.show'])->only(['viewUserCart']);
    }



    public function viewUserCart($userId)
    {
        try {
            $cartProducts = $this->cartRepository->getProducts($userId)->paginate();
            $data = [
                'cartProducts' => new CartProductCollection($cartProducts),
                'sum_price' => $cartProducts->sum('price'),
                'sum_tax' => $cartProducts->sum('tax'),
                'sum_subtotal' => $cartProducts->sum('subtotal'),
                'sum_quantity' => $cartProducts->sum('quantity'),
            ];
            return $this->successResponse($data);
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }



}
