<?php

namespace Modules\Product\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Modules\Product\Http\Requests\Admin\RelatedProduct\DeleteRelatedProductsRequest;
use Modules\Product\Http\Requests\Admin\RelatedProduct\StoreRelatedProductsRequest;
use Modules\Product\Http\Requests\Admin\RelatedProduct\UpdateRelatedProductsRequest;
use Modules\Product\Repositories\ProductRepository;
use Modules\Product\Transformers\Admin\Product\ProductCollection;

class RelatedProductController extends Controller
{
    use ApiResponseTrait;
    protected $productRepository;
    protected $_config;
    protected $guard;
    public function __construct(ProductRepository $productRepository)
    {
        $this->guard = 'admin-api';
        request()->merge(['token' => 'true']);
        Auth::setDefaultDriver($this->guard);
        $this->_config = request('_config');
        $this->productRepository = $productRepository;
        // permissions
        $this->middleware('auth:' . $this->guard);
        $this->middleware(['permission:products.show'])->only(['getRelatedProducts']);
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
    public function getRelatedProducts($productId)
    {
        try {
            $product = $this->productRepository->findOrFail($productId);
            $data = $this->productRepository->getPaginatedRelatedProducts($product)->paginate();
            return $this->successResponse(new ProductCollection($data));
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
    public function store(StoreRelatedProductsRequest $request)
    {
        try {
            $data = $request->validated();
            $created = $this->productRepository->addRelatedProducts($data);

            if ($created) {
                return $this->messageResponse(
                    __("product::app.relatedProducts.created-successfully"),
                    true,
                    201
                );
            }{
                return $this->messageResponse(
                    __("product::app.relatedProducts.created-failed"),
                    false,
                    400
                );
            }
        } catch (Exception $e) {
            //    return  $this->messageResponse( $e->getMessage());
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
    public function update(UpdateRelatedProductsRequest $request)
    {
        try {

            $data = $request->validated();
            $updated = $this->productRepository->syncRelatedProducts($data);
            if ($updated) {
                return $this->messageResponse(
                    __("product::app.relatedProducts.updated-successfully"),
                    true,
                    200
                );
            }{
                return $this->messageResponse(
                    __("product::app.relatedProducts.updated-failed"),
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
    public function destroy(DeleteRelatedProductsRequest $request)
    {
        try {
            $data = $request->validated();

            $deleted = $this->productRepository->removeRelatedProducts($data);
            if ($deleted) {
                return $this->messageResponse(
                    __("product::app.relatedProducts.deleted-successfully"),
                    true,
                    200
                );
            }{
                return $this->messageResponse(
                    __("product::app.relatedProducts.deleted-failed"),
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
