<?php

namespace Modules\Product\Http\Controllers\Api;

use Exception;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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

    /**
     * Get current locale for caching
     */
    private function getCurrentLocale(): string
    {
        return app()->getLocale() ?? 'en';
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            request('per_page', 6);
            $locale = $this->getCurrentLocale();

            // Use cached active products with pagination
            $products = $this->productRepository->getCachedActiveProducts($locale);

            // Check if products are empty (for both paginated and collection results)
            $isEmpty = method_exists($products, 'isEmpty') ?
                $products->isEmpty() :
                (method_exists($products, 'count') ? $products->count() === 0 : empty($products));

            if ($isEmpty) {
                return $this->successResponse(
                    [],
                    __('app.no-data-found')
                );
            }

            // Convert collection to paginator if needed (for cached results)
            if (!method_exists($products, 'total')) {
                $page = request()->get('page', 1);
                $perPage = request()->get('per_page', 15);
                $products = new \Illuminate\Pagination\LengthAwarePaginator(
                    $products->forPage($page, $perPage),
                    $products->count(),
                    $perPage,
                    $page
                );
            }

            return $this->successResponse(new ProductCollection($products));

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
     * Get featured products
     */
    public function getFeaturedProducts()
    {
        try {
            $locale = $this->getCurrentLocale();

            // Use cached featured products
            $products = $this->productRepository->getCachedFeaturedProducts($locale);

            if ($products->isEmpty()) {
                return $this->successResponse(
                    [],
                    __('app.no-data-found')
                );
            }

            return $this->successResponse(ProductResource::collection($products));
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }

    /**
     * Get new arrival products
     */
    public function getNewArrivals()
    {
        try {
            $locale = $this->getCurrentLocale();

            // Use cached new arrival products
            $products = $this->productRepository->getCachedNewArrivals($locale);

            if ($products->isEmpty()) {
                return $this->successResponse(
                    [],
                    __('app.no-data-found')
                );
            }

            return $this->successResponse(ProductResource::collection($products));
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }

    /**
     * Get best seller products
     */
    public function getBestSellers()
    {
        try {
            $locale = $this->getCurrentLocale();

            // Use cached best seller products
            $products = $this->productRepository->getCachedBestSellers($locale);

            if ($products->isEmpty()) {
                return $this->successResponse(
                    [],
                    __('app.no-data-found')
                );
            }

            return $this->successResponse(ProductResource::collection($products));
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }

    /**
     * Get top products
     */
    public function getTopProducts()
    {
        try {
            $locale = $this->getCurrentLocale();

            // Use cached top products
            $products = $this->productRepository->getCachedTopProducts($locale);

            if ($products->isEmpty()) {
                return $this->successResponse(
                    [],
                    __('app.no-data-found')
                );
            }

            return $this->successResponse(ProductResource::collection($products));
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }

    /**
     * Get products by category
     */
    public function getByCategory($categoryId)
    {
        try {
            $locale = $this->getCurrentLocale();

            // Use cached products by category
            $products = $this->productRepository->getCachedProductsByCategory($categoryId, $locale);

            if ($products->isEmpty()) {
                return $this->successResponse(
                    [],
                    __('app.no-data-found')
                );
            }

            return $this->successResponse(ProductResource::collection($products));
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }

    /**
     * Get products by type
     */
    public function getByType($type)
    {
        try {
            $locale = $this->getCurrentLocale();

            // Use cached products by type
            $products = $this->productRepository->getCachedProductsByType($type, $locale);

            if ($products->isEmpty()) {
                return $this->successResponse(
                    [],
                    __('app.no-data-found')
                );
            }

            return $this->successResponse(ProductResource::collection($products));
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
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }

    /**
     * Show product by slug
     */
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