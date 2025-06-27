<?php

namespace Modules\Wishlist\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Modules\Wishlist\Http\Requests\Api\Wishlist\AddToWishlistRequest;
use Modules\Wishlist\Transformers\Api\WishlistProduct\WishlistProductCollection;
use Modules\Wishlist\Repositories\WishlistRepository;

class WishlistController extends Controller
{
    use ApiResponseTrait;
    protected $wishlistRepository;
    protected $_config;
    protected $guard;
    public function __construct(WishlistRepository $wishlistRepository)
    {
        $this->guard = 'user-api';
        request()->merge(['token' => 'true']);
        Auth::setDefaultDriver($this->guard);
        $this->_config = request('_config');
        $this->wishlistRepository = $wishlistRepository;
        // permissions
        $this->middleware('auth:' . $this->guard);
    }

    public function viewWishlist()
    {
        try {
            $userId = Auth::guard('user-api')->id();

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
    public function addToWishlist(AddToWishlistRequest $request)
    {
        try {
            $data = $request->validated();
            $userId = auth()->id();
            $added = $this->wishlistRepository->addProduct($data, $userId);

            if ($added) {
                return $this->messageResponse(
                    __("wishlist::app.wishlistProducts.created-successfully"),
                    true,
                    201
                );
            } {
                return $this->messageResponse(
                    __("wishlist::app.wishlistProducts.created-failed"),
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

    public function removeFromWishlist($id)
    {
        try {
            $userId = auth()->id();

            $removed = $this->wishlistRepository->removeProduct($id, $userId);
            if ($removed) {
                return $this->messageResponse(
                    __("wishlist::app.wishlistProducts.deleted-successfully"),
                    true,
                    201
                );
            } {
                return $this->messageResponse(
                    __("wishlist::app.wishlistProducts.deleted-failed"),
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


    public function clearWishlist()
    {
        try {
            $wishlistId = auth()->user()->wishlist->id;
            $removed = $this->wishlistRepository->emptyWishlist($wishlistId);
            if ($removed) {
                return $this->messageResponse(
                    __("wishlist::app.wishlist.cleared-successfully"),
                    true,
                    201
                );
            } {
                return $this->messageResponse(
                    __("wishlist::app.wishlist.cleared-failed"),
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
