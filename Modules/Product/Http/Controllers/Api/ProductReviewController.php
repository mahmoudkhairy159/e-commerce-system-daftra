<?php

namespace Modules\Product\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Modules\Product\Http\Requests\Api\ProductReview\StoreProductReviewRequest;
use Modules\Product\Http\Requests\Api\ProductReview\UpdateProductReviewRequest;
use Modules\Product\Repositories\ProductReviewRepository;
use Modules\Product\Transformers\Api\ProductReview\ProductReviewStatisticsResource;
use Modules\Product\Transformers\Api\ProductReview\ProductReviewCollection;
use Modules\Product\Transformers\Api\ProductReview\ProductReviewResource;

class ProductReviewController extends Controller
{
    use ApiResponseTrait;

    protected $productReviewRepository;

    protected $_config;
    protected $guard;

    public function __construct(ProductReviewRepository $productReviewRepository)
    {
        $this->guard = 'user-api';
        request()->merge(['token' => 'true']);
        Auth::setDefaultDriver($this->guard);
        $this->_config = request('_config');
        $this->productReviewRepository = $productReviewRepository;
        // permissions
        $this->middleware('auth:' . $this->guard)->except([
            'getByProductId',
            'getByUserId',
            'show',
        ]);
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
            $data = $this->productReviewRepository->getOneById($id);
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
     * Store a newly created resource in storage.
     */
    public function store(StoreProductReviewRequest $request)
    {
        try {
            $data = $request->validated();
            $data['user_id'] = auth()->guard($this->guard)->id();
            $created = $this->productReviewRepository->create($data);
            if ($created) {
                return $this->successResponse(
                    new ProductReviewResource($created),
                    __("product::app.productReviews.created-successfully"),
                    201
                );

            }{
                return $this->messageResponse(
                    __("product::app.productReviews.created-failed"),
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




    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductReviewRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $data['user_id'] = auth()->guard($this->guard)->id();
            $updated = $this->productReviewRepository->updateOne($data, $id);

            if ($updated) {
                return $this->messageResponse(
                    __("product::app.productReviews.updated-successfully"),
                    true,
                    200
                );

            }{
                return $this->messageResponse(
                    __("product::app.productReviews.updated-failed"),
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
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $deleted = $this->productReviewRepository->deleteOneByUser($id);
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
