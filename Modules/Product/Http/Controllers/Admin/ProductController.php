<?php

namespace Modules\Product\Http\Controllers\Admin;

use App\Traits\CacheTrait;
use Exception;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Product\Http\Requests\Admin\Product\BulkUpdateStatusRequest;
use Modules\Product\Http\Requests\Admin\Product\ChangeApprovalStatusRequest;
use Modules\Product\Http\Requests\Admin\Product\StoreProductRequest;
use Modules\Product\Http\Requests\Admin\Product\UpdateProductRequest;
use Modules\Product\Http\Requests\Admin\Product\UpdateProductPositionRequest;
use Modules\Product\Http\Requests\Admin\Product\UpdateProductTypeRequest;
use Modules\Product\Models\Product;
use Modules\Product\Repositories\ProductRepository;
use Modules\Product\Transformers\Admin\Product\ProductCollection;
use Modules\Product\Transformers\Admin\Product\ProductResource;

class ProductController extends Controller
{
    use ApiResponseTrait;
    use CacheTrait;
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
        $this->middleware(['permission:products.show'])->only(['index', 'show', 'showBySlug', 'getAllPendingProducts']);
        $this->middleware(['permission:products.create'])->only(['store']);
        $this->middleware(['permission:products.update'])->only(['update', 'changeStatus', 'changeApprovalStatus', 'updateProductType', 'updatePosition', 'bulkUpdateStatus']);
        $this->middleware(['permission:products.destroy'])->only(['destroy', 'deleteImage', 'forceDelete', 'restore', 'getOnlyTrashed']);
    }
    public function index()
    {
        try {
            $data = $this->productRepository->getAll()->paginate();
            //dd($data);
            return $this->successResponse(new ProductCollection($data));
        } catch (Exception $e) {
            dd($e->getMessage());
            return $this->errorResponse([], __('app.something-went-wrong'), 500);
        }
    }





    public function getAllPendingProducts()
    {
        try {
            $data = $this->productRepository->getAllByApprovalStatus(Product::APPROVAL_STATUS_PENDING)->paginate();
            return $this->successResponse(new ProductCollection($data));
        } catch (Exception $e) {
            dd($e->getMessage());
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }
    public function getStatistics()
    {
        try {
            $data = $this->productRepository->getStatistics();
            return $this->successResponse($data);
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

    public function store(StoreProductRequest $request)
    {
        try {
            $data = $request->validated();
            $data['created_by'] = auth()->guard($this->guard)->id();
            $created = $this->productRepository->createOneByAdmin($data);
            if ($created) {
                return $this->messageResponse(
                    __("product::app.products.created-successfully"),
                    true,
                    201
                );
            } {
                return $this->messageResponse(
                    __("product::app.products.created-failed"),
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
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $data = $this->productRepository->getOneById($id);
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

            return $this->errorResponse([], __('app.something-went-wrong'), 500);
        }
    }

    /**
     * Display a resource by slug.
     */
    public function showBySlug(string $slug)
    {
        try {
            $data = $this->productRepository->findBySlug($slug);
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

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, $id)
    {
        try {

            $data = $request->validated();
            $data['updated_by'] = auth()->guard($this->guard)->id();
            $updated = $this->productRepository->updateOneByAdmin($data, $id);
            if ($updated) {
                return $this->messageResponse(
                    __("product::app.products.updated-successfully"),
                    true,
                    200
                );
            } {
                return $this->messageResponse(
                    __("product::app.products.updated-failed"),
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
     * Change the status of the specified resource.
     */
    public function changeStatus($id)
    {

        try {

            $data['updated_by'] = auth()->guard($this->guard)->id();
            $updated = $this->productRepository->changeStatus($id);
            if ($updated) {
                return $this->messageResponse(
                    __("app.products.updated-successfully"),
                    true,
                    200
                );
            } {
                return $this->messageResponse(
                    __("product::app.products.updated-failed"),
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



    public function changeApprovalStatus(ChangeApprovalStatusRequest $request, $id)
    {

        try {
            $data = $request->validated();
            $data['updated_by'] = auth()->guard($this->guard)->id();
            $updated = $this->productRepository->changeApprovalStatus($id, $data);
            if ($updated) {
                return $this->messageResponse(
                    __("product::app.products.updated-successfully"),
                    true,
                    200
                );
            } {
                return $this->messageResponse(
                    __("product::app.products.updated-failed"),
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

    public function updateProductType(UpdateProductTypeRequest $request, $id)
    {

        try {
            $data = $request->validated();
            $data['updated_by'] = auth()->guard($this->guard)->id();
            $updated = $this->productRepository->updateProductType($id, $data);
            if ($updated) {
                return $this->messageResponse(
                    __("product::app.products.updated-successfully"),
                    true,
                    200
                );
            } {
                return $this->messageResponse(
                    __("product::app.products.updated-failed"),
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
    public function updatePosition(UpdateProductPositionRequest $request, $id)
    {

        try {

            $data = $request->validated();
            $data['updated_by'] = auth()->guard($this->guard)->id();
            $updated = $this->productRepository->updatePosition($data, $id);
            if ($updated) {
                return $this->messageResponse(
                    __("product::app.products.updated-successfully"),
                    true,
                    200
                );
            } {
                return $this->messageResponse(
                    __("product::app.products.updated-failed"),
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
     * Bulk update the status of multiple products.
     */
    public function bulkUpdateStatus(BulkUpdateStatusRequest $request)
    {
        try {
            // Validate the request data
            $data = $request->validated();

            // Call the bulk update method from the repository
            $updated = $this->productRepository->bulkUpdateStatus($data['ids'], $data['status']);

            // Check if the update was successful
            if ($updated) {
                return $this->messageResponse(__('product::app.products.updated-successfully'), true, 200);
            } else {
                return $this->messageResponse(__('product::app.products.update-failed'), false, 400);
            }
        } catch (Exception $e) {
            return $this->errorResponse([], __('app.something-went-wrong'), 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $deleted = $this->productRepository->deleteOneByAdmin($id);

            if ($deleted) {
                return $this->messageResponse(
                    __("product::app.products.deleted-successfully"),
                    true,
                    200
                );
            } {
                return $this->messageResponse(
                    __("product::app.products.deleted-failed"),
                    false,
                    400
                );
            }
        } catch (Exception $e) {
            return $this->errorResponse([], __('app.something-went-wrong'), 500);
        }
    }
    public function deleteImage($id)
    {
        try {
            $deleted = $this->productRepository->deleteImage($id);
            if ($deleted) {
                return $this->messageResponse(
                    __("product::app.products.deleted-successfully"),
                    true,
                    200
                );
            } {
                return $this->messageResponse(
                    __("product::app.products.deleted-failed"),
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
     * Get only trashed resources.
     */
    public function getOnlyTrashed()
    {
        try {
            $data = $this->productRepository->getOnlyTrashed()->paginate();
            return $this->successResponse(new ProductCollection($data));
        } catch (Exception $e) {
            return $this->errorResponse([], __('app.something-went-wrong'), 500);
        }
    }

    /**
     * Permanently delete a resource.
     */
    public function forceDelete($id)
    {
        try {
            $deleted = $this->productRepository->forceDelete($id);

            return $deleted
                ? $this->messageResponse(__('product::app.products.deleted-successfully'), true, 200)
                : $this->messageResponse(__('product::app.products.deleted-failed'), false, 400);
        } catch (Exception $e) {
            return $this->errorResponse([], __('app.something-went-wrong'), 500);
        }
    }

    /**
     * Restore a trashed resource.
     */
    public function restore($id)
    {
        try {
            $restored = $this->productRepository->restore($id);
            return $restored
                ? $this->messageResponse(__('product::app.products.restored-successfully'), true, 200)
                : $this->messageResponse(__('product::app.products.restored-failed'), false, 400);
        } catch (Exception $e) {
            return $this->errorResponse([], __('app.something-went-wrong'), 500);
        }
    }

}
