<?php

namespace Modules\Product\Http\Controllers\Api;

use Exception;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Product\Enums\ProductTypeEnum;
use Modules\Product\Repositories\ProductRepository;
use Modules\Product\Transformers\Api\Product\ProductCollection;
use Modules\Product\Transformers\Api\Product\ProductResource;

class ProductController extends Controller
{
    use ApiResponseTrait;
    protected $productRepository;
    protected $_config;
    protected $guard;
    public function __construct(ProductRepository $productRepository)
    {
        $this->guard = 'user-api';
        request()->merge(['token' => 'true']);
        Auth::setDefaultDriver($this->guard);
        $this->_config = request('_config');
        $this->productRepository = $productRepository;
        // permissions
        //$this->middleware('auth:' . $this->guard);
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
            $data = $this->productRepository->getAllActive()->paginate();
            return $this->successResponse(new  ProductCollection($data));
        } catch (Exception $e) {
            dd($e->getMessage());

            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }
    public function getFeaturedProducts()
    {
        try {
            $type=ProductTypeEnum::FEATURED;
            $data = $this->productRepository->getProductByType($type)->get();
            return $this->successResponse(ProductResource::collection($data));
        } catch (Exception $e) {
            dd($e->getMessage());

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
            $data = $this->productRepository->getOneActiveById($id);
            if (!$data) {
                return $this->errorResponse(
                    [],
                    __('app.data-not-found'),
                    404
                );
            }
            return $this->successResponse(new ProductResource($data));
        } catch (Exception $e) {
            dd($e->getMessage());

            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }
    public function showBySlug(string $slug)
    {
        try {
            $data = $this->productRepository->findActiveBySlug($slug);
            if (!$data) {
                return $this->errorResponse(
                    [],
                    __('app.data-not-found'),
                    404
                );
            }
            return $this->successResponse(new ProductResource($data));
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }




}