<?php

namespace Modules\Wishlist\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Modules\Wishlist\Transformers\Admin\WishlistProduct\WishlistProductCollection;
use Modules\Wishlist\Repositories\WishlistRepository;

class WishlistController extends Controller
{
    use ApiResponseTrait;
    protected $wishlistRepository;
    protected $_config;
    protected $guard;
    public function __construct(WishlistRepository $wishlistRepository)
    {
        $this->guard = 'admin-api';
        request()->merge(['token' => 'true']);
        Auth::setDefaultDriver($this->guard);
        $this->_config = request('_config');
        $this->wishlistRepository = $wishlistRepository;
        // permissions
        $this->middleware('auth:' . $this->guard);
        $this->middleware(['permission:wishlist.show'])->only(['viewUserWishlist']);
    }



    public function viewUserWishlist($userId)
    {
            try {
                $data = $this->wishlistRepository->getWishlistProducts($userId)->paginate();
                return $this->successResponse(new WishlistProductCollection($data));
            } catch (Exception $e) {
                return $this->errorResponse(
                    [],
                    __('app.something-went-wrong'),
                    500
                );
            }
    }



}
