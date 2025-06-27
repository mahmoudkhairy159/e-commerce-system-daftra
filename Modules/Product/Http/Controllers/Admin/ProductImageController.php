<?php

namespace Modules\Product\Http\Controllers\Admin;

use Exception;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Product\Http\Requests\Admin\ProductImage\StoreProductImageRequest;
use Modules\Product\Http\Requests\Admin\ProductImage\UpdateProductImageRequest;
use Modules\Product\Repositories\ProductImageRepository;
use Modules\Product\Transformers\Admin\ProductImage\ProductImageResource;

class ProductImageController extends Controller
{
    use ApiResponseTrait;
    protected $productImageRepository;
    protected $_config;
    protected $guard;
    public function __construct(ProductImageRepository $productImageRepository)
    {
        $this->guard = 'admin-api';
        request()->merge(['token' => 'true']);
        Auth::setDefaultDriver($this->guard);
        $this->_config = request('_config');
        $this->productImageRepository = $productImageRepository;
        // permissions
        $this->middleware('auth:' . $this->guard);
        $this->middleware(['permission:products.show'])->only(['getByProductId']);
        $this->middleware(['permission:products.create'])->only(['store']);
        $this->middleware(['permission:products.update'])->only(['update']);
        $this->middleware(['permission:products.destroy'])->only(['destroy']);
    }
    /**Introduction
    Issues
    Changelog
    FAQ

     * Display a listing of the resource.
     */
    public function getByProductId($productId)
    {
        try {
            $data = $this->productImageRepository->getByProductId($productId)->get();
            return $this->successResponse( ProductImageResource::collection($data));
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
    public function store(StoreProductImageRequest $request)
    {
        try {
            $data =  $request->validated();
            $data['created_by'] = auth()->guard($this->guard)->id();
            $created = $this->productImageRepository->createOne($data);

            if ($created) {
                return $this->messageResponse(
                    __("product::app.productImages.created-successfully"),
                    true,
                    201
                );
            } {
                return $this->messageResponse(
                    __("product::app.productImages.created-failed"),
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
     * Show the specified resource.
     */
    public function show($id)
    {
        try {
            $data = $this->productImageRepository->findOrFail($id);
            return $this->successResponse(new ProductImageResource($data));
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
    public function update(UpdateProductImageRequest $request, $id)
    {
        try {

            $data =  $request->validated();
            $data['updated_by'] = auth()->guard($this->guard)->id();
            $updated = $this->productImageRepository->updateOne($data, $id);
            if ($updated) {
                return $this->messageResponse(
                    __("product::app.productImages.updated-successfully"),
                    true,
                    200
                );
            } {
                return $this->messageResponse(
                    __("product::app.productImages.updated-failed"),
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
            $deleted = $this->productImageRepository->deleteOne($id);
            if ($deleted) {
                return $this->messageResponse(
                    __("product::app.productImages.deleted-successfully"),
                    true,
                    200
                );
            } {
                return $this->messageResponse(
                    __("product::app.productImages.deleted-failed"),
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
