<?php

namespace Modules\Product\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Modules\Product\Repositories\ProductReviewRepository;
use Modules\Product\Transformers\Admin\ProductReview\ProductReviewCollection;
use Modules\Product\Transformers\Admin\ProductReview\ProductReviewResource;
use Modules\Product\Transformers\Admin\ProductReview\ProductReviewStatisticsResource;

class ProductReviewController extends Controller
{
    use ApiResponseTrait;

    protected $productReviewRepository;

    protected $_config;
    protected $guard;

    public function __construct(ProductReviewRepository $productReviewRepository)
    {
        $this->guard = 'admin-api';
        request()->merge(['token' => 'true']);
        Auth::setDefaultDriver($this->guard);
        $this->_config = request('_config');
        $this->productReviewRepository = $productReviewRepository;
        // permissions
        $this->middleware('auth:' . $this->guard);
        $this->middleware(['permission:productReviews.show'])->only(['getByProductId','getByUserId','show']);
        $this->middleware(['permission:productReviews.destroy'])->only(['destroy']);
    }

    public function getByProductId($productId)
    {
        try {
            // Get paginated reviews
            $reviews = $this->productReviewRepository->getByProductId($productId)->paginate();

            // Get review statistics
            $statistics = $this->productReviewRepository->getReviewStatistics($productId);

            // Combine the data
            $response = [
                'reviews' => new ProductReviewCollection($reviews),
                'statistics' => new ProductReviewStatisticsResource($statistics)
            ];

            return $this->successResponse($response);
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }

    public function getByUserId($userId)
    {
        try {
            $data = $this->productReviewRepository->getByUserId($userId)->paginate();
            return $this->successResponse(new ProductReviewCollection($data));
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
            $data = $this->productReviewRepository->findOrFail($id);
            return $this->successResponse(new ProductReviewResource($data));
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
            $deleted = $this->productReviewRepository->deleteOne($id);
            if ($deleted) {
                return $this->messageResponse(
                    __("product::app.productReviews.deleted-successfully"),
                    true,
                200
                );
            }{
                return $this->messageResponse(
                    __("product::app.productReviews.deleted-failed"),
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
