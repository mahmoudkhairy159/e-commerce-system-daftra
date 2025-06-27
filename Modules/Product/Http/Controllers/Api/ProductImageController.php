<?php

namespace Modules\Product\Http\Controllers\Api;

use Exception;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Product\Http\Requests\Api\ProductImage\StoreProductImageRequest;
use Modules\Product\Http\Requests\Api\ProductImage\UpdateProductImageRequest;
use Modules\Product\Repositories\ProductImageRepository;
use Modules\Product\Transformers\Api\ProductImage\ProductImageResource;

class ProductImageController extends Controller
{
    use ApiResponseTrait;
    protected $productImageRepository;
    protected $_config;
    protected $guard;
    public function __construct(ProductImageRepository $productImageRepository)
    {
        $this->guard = 'user-api';
        request()->merge(['token' => 'true']);
        Auth::setDefaultDriver($this->guard);
        $this->_config = request('_config');
        $this->productImageRepository = $productImageRepository;
        // permissions
        //$this->middleware('auth:' . $this->guard);

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





}
