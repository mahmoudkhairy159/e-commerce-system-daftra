<?php

namespace Modules\Product\Repositories;

use App\Traits\SoftDeletableTrait;
use App\Traits\CacheTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Product\Enums\ProductTypeEnum;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductImage;
use Prettus\Repository\Eloquent\BaseRepository;

class ProductRepository extends BaseRepository
{
    use SoftDeletableTrait;
    use CacheTrait;

    public $retrievedData = [
        'id',
        'code',
        'image',
        'video_url',
        'type',
        'created_by',
        'updated_by',
        'status',
        'position',
        'currency',
        'stock',
        'price',
        'offer_price',
        'tax_rate',
        'offer_start_date',
        'offer_end_date',
        'approval_status',
        'created_at',
        'updated_at',
    ];

    public function model()
    {
        return Product::class;
    }

    /***************************************** Retrieving For Admins **************************************/

    public function getAll()
    {
        return $this->model
            ->withCount('orders', 'productReviews')
            ->withAvg('productReviews', 'rating')
            ->filter(request()->all())
            ->orderBy('created_at', 'desc');
    }

    public function getAllByApprovalStatus($approvalStatus)
    {
        return $this->model
            ->where('approval_status', $approvalStatus)
            ->withCount('orders', 'productReviews')
            ->withAvg('productReviews', 'rating')
            ->filter(request()->all())
            ->orderBy('created_at', 'desc');
    }

    public function getStatistics()
    {
        return [
            "products_count" => $this->model->count(),
            "active_products_count" => $this->model->where('status', Product::STATUS_ACTIVE)->count(),
        ];
    }

    /***************************************** Retrieving For Users **************************************/

    public function getAllActive()
    {
        return $this->model
            ->filter(request()->all())
            ->active()
            ->orderBy('position', 'asc');
    }

    public function getProductByType($type)
    {
        return $this->model
            ->filter(request()->all())
            ->active()
            ->where('type', $type);
    }

    /***************************************** Cached Methods **************************************/

    /**
     * Get cached all products with filter support
     */
    public function getCachedProducts()
    {
        // If filters are present, query database directly with filters
        if (!$this->shouldUseCache()) {
            return $this->getAllFiltered();
        }

        return app('cache.products')->getAll();
    }

    /**
     * Get cached active products with filter support
     */
    public function getCachedActiveProducts(string $locale = null)
    {
        // If filters are present, query database directly with filters
        if (!$this->shouldUseCache()) {
            return $this->getAllActiveFiltered();
        }

        return app('cache.products')->getAllActive($locale);
    }

    /**
     * Get cached products by type with filter support
     */
    public function getCachedProductsByType(int $type, string $locale = null)
    {
        // If filters are present, query database directly with filters
        if (!$this->shouldUseCache()) {
            return $this->getProductByTypeFiltered($type);
        }

        return app('cache.products')->getByType($type, $locale);
    }

    /**
     * Get cached featured products with filter support
     */
    public function getCachedFeaturedProducts(string $locale = null)
    {
        // If filters are present, query database directly with filters
        if (!$this->shouldUseCache()) {
            return $this->getFeaturedProductsFiltered();
        }

        return app('cache.products')->getFeatured($locale);
    }

    /**
     * Get cached new arrival products with filter support
     */
    public function getCachedNewArrivals(string $locale = null)
    {
        // If filters are present, query database directly with filters
        if (!$this->shouldUseCache()) {
            return $this->getNewArrivalsFiltered();
        }

        return app('cache.products')->getNewArrivals($locale);
    }

    /**
     * Get cached best seller products with filter support
     */
    public function getCachedBestSellers(string $locale = null)
    {
        // If filters are present, query database directly with filters
        if (!$this->shouldUseCache()) {
            return $this->getBestSellersFiltered();
        }

        return app('cache.products')->getBestSellers($locale);
    }

    /**
     * Get cached top products with filter support
     */
    public function getCachedTopProducts(string $locale = null)
    {
        // If filters are present, query database directly with filters
        if (!$this->shouldUseCache()) {
            return $this->getTopProductsFiltered();
        }

        return app('cache.products')->getTopProducts($locale);
    }

    /**
     * Get cached products by category with filter support
     */
    public function getCachedProductsByCategory(int $categoryId, string $locale = null)
    {
        // If filters are present, query database directly with filters
        if (!$this->shouldUseCache()) {
            return $this->getProductsByCategoryFiltered($categoryId);
        }

        return app('cache.products')->getByCategory($categoryId, $locale);
    }

    /*****************************************Filtered Query Methods ********************************************/

    /**
     * Get all products with filters applied
     */
    private function getAllFiltered()
    {
        return $this->model
            ->with(['categories', 'productImages'])
            ->filter(request()->all())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get active products with filters applied
     */
    private function getAllActiveFiltered()
    {
        $perPage = request()->get('per_page', 15);

        return $this->model
            ->active()
            ->with(['categories', 'productImages'])
            ->filter(request()->all())
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get products by type with filters applied
     */
    private function getProductByTypeFiltered(int $type)
    {
        return $this->model
            ->active()
            ->where('type', $type)
            ->with(['categories', 'productImages'])
            ->filter(request()->all())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get featured products with filters applied
     */
    private function getFeaturedProductsFiltered()
    {
        return $this->model
            ->active()
            ->where('type', ProductTypeEnum::FEATURED) // 1 = FEATURED
            ->with(['categories', 'productImages'])
            ->filter(request()->all())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get new arrivals with filters applied
     */
    private function getNewArrivalsFiltered()
    {
        return $this->model
            ->active()
            ->where('type', ProductTypeEnum::NEW_ARRIVAL) // 0 = NEW_ARRIVAL
            ->with(['categories', 'productImages'])
            ->filter(request()->all())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get best sellers with filters applied
     */
    private function getBestSellersFiltered()
    {
        return $this->model
            ->active()
            ->where('type', ProductTypeEnum::BEST_SELLER) // 3 = BEST_SELLER
            ->with(['categories', 'productImages'])
            ->filter(request()->all())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get top products with filters applied
     */
    private function getTopProductsFiltered()
    {
        return $this->model
            ->active()
            ->where('type', ProductTypeEnum::TOP_PRODUCT) // 2 = TOP_PRODUCT
            ->with(['categories', 'productImages'])
            ->filter(request()->all())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get products by category with filters applied
     */
    private function getProductsByCategoryFiltered(int $categoryId)
    {
        return $this->model
            ->active()
            ->whereHas('categories', function ($query) use ($categoryId) {
                $query->where('categories.id', $categoryId);
            })
            ->with(['categories', 'productImages'])
            ->filter(request()->all())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /*****************************************End Cached Methods **************************************/

    public function findBySlug(string $slug)
    {
        return $this->model->where('slug', $slug)
            ->with([
                'categories',
                'productImages',
                'relatedProducts',
                'accessories',
            ])
            ->withCount('orders', 'productReviews')
            ->withAvg('productReviews', 'rating')
            ->first();
    }

    public function getOneById(string $id)
    {
        return $this->model->where('id', $id)
            ->with([
                'categories',
                'productImages',
                'relatedProducts',
                'accessories',
            ])
            ->withCount('orders', 'productReviews')
            ->withAvg('productReviews', 'rating')
            ->first();
    }

    public function findActiveBySlug(string $slug)
    {
        return $this->model
            ->active()
            ->where('slug', $slug)
            ->with([
                'categories',
                'productImages',
                'relatedProducts',
                'accessories',
            ])
            ->withAvg('productReviews', 'rating')
            ->first();
    }

    public function getOneActiveById(string $id)
    {
        return $this->model
            ->active()
            ->where('id', $id)
            ->with([
                'categories',
                'productImages',
                'relatedProducts',
                'accessories',
            ])
            ->first();
    }

    public function createOneByAdmin(array $data)
    {
        try {
            DB::beginTransaction();

            if (request()->hasFile('image')) {
                $data['image'] = $this->uploadFile(request()->file('image'), Product::FILES_DIRECTORY);
            }
            if (request()->hasFile('product_images')) {
                $images = request()->file('product_images');
                $uploadedImages = [];
                foreach ($images as $image) {
                    $uploadedImages[] = $this->uploadFile($image, ProductImage::FILES_DIRECTORY);
                }
            }
            $created = $this->model->create($data);
            if (isset($uploadedImages)) {
                foreach ($uploadedImages as $imagePath) {
                    ProductImage::create([
                        'product_id' => $created->id,
                        'image' => $imagePath,
                    ]);
                }
            }

            $created->categories()->attach($data['categoryIds']);
            $data['product_id'] = $created->id;
            if (isset($data['relatedProductIds']) && is_array($data['relatedProductIds'])) {
                $this->syncRelatedProducts($data);
            }
            if (isset($data['productAccessoriesIds']) && is_array($data['productAccessoriesIds'])) {
                $this->syncProductAccessories($data);
            }

            // Invalidate product caches
            $this->invalidateProductCaches($data);

            DB::commit();
            return $created;
        } catch (\Throwable $th) {
            dd($th->getMessage());
            DB::rollBack();
            return false;
        }
    }

    /**
     * Create a new product with cache invalidation
     */
    public function create(array $attributes)
    {
        try {
            DB::beginTransaction();

            $product = parent::create($attributes);

            // Invalidate product caches
            $this->invalidateProductCaches($attributes);

            DB::commit();
            return $product;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function updateOneByAdmin(array $data, int $id)
    {
        try {
            DB::beginTransaction();
            $product = $this->model->findOrFail($id);
            $oldType = $product->type;
            $oldCategoryIds = $product->categories->pluck('id')->toArray();

            if (request()->hasFile('image')) {
                if ($product->image) {
                    $this->deleteFile($product->image);
                }
                $data['image'] = $this->uploadFile(request()->file('image'), Product::FILES_DIRECTORY);
            }
            $updated = $product->update($data);
            $data['product_id'] = $product->id;

            if (isset($data['categoryIds']) && is_array($data['categoryIds'])) {
                $product->categories()->sync($data['categoryIds']);
            }
            if (isset($data['relatedProductIds']) && is_array($data['relatedProductIds'])) {
                $this->syncRelatedProducts($data);
            }
            if (isset($data['productAccessoriesIds']) && is_array($data['productAccessoriesIds'])) {
                $this->syncProductAccessories($data);
            }

            // Invalidate product caches
            $this->invalidateProductCaches($data, $oldType, $oldCategoryIds);

            DB::commit();
            return $product->refresh();
        } catch (\Throwable $th) {
            dd($th->getMessage());
            DB::rollBack();
            return false;
        }
    }

    /**
     * Update a product with cache invalidation
     */
    public function update(array $attributes, $id)
    {
        try {
            DB::beginTransaction();

            $product = $this->find($id);
            $oldType = $product->type;
            $oldCategoryIds = $product->categories->pluck('id')->toArray();

            $updated = parent::update($attributes, $id);

            // Invalidate product caches
            $this->invalidateProductCaches($attributes, $oldType, $oldCategoryIds);

            DB::commit();
            return $updated;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Delete a product with cache invalidation
     */
    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $product = $this->find($id);
            $type = $product->type;
            $categoryIds = $product->categories->pluck('id')->toArray();

            $deleted = parent::delete($id);

            // Invalidate product caches
            $this->invalidateProductCaches(['type' => $type], $type, $categoryIds);

            DB::commit();
            return $deleted;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Invalidate product caches based on product data
     */
    private function invalidateProductCaches(array $data, int $oldType = null, array $oldCategoryIds = [])
    {
        // Invalidate all product caches
        app('cache.products')->invalidateAll();

        // Invalidate specific type caches
        if (isset($data['type'])) {
            app('cache.products')->invalidate(null, $data['type']);
        }
        if ($oldType !== null && $oldType !== ($data['type'] ?? null)) {
            app('cache.products')->invalidate(null, $oldType);
        }

        // Invalidate category-specific caches
        if (isset($data['categoryIds']) && is_array($data['categoryIds'])) {
            foreach ($data['categoryIds'] as $categoryId) {
                app('cache.products')->invalidate($categoryId);
            }
        }

        // Invalidate old category caches
        foreach ($oldCategoryIds as $categoryId) {
            app('cache.products')->invalidate($categoryId);
        }
    }

    public function changeStatus(int $id)
    {
        try {
            DB::beginTransaction();
            $product = $this->model->findOrFail($id);
            $product->status = $product->status == Product::STATUS_ACTIVE ? Product::STATUS_INACTIVE : Product::STATUS_ACTIVE;
            $updated = $product->save();

            // Invalidate product caches
            $this->invalidateProductCaches(['type' => $product->type], null, $product->categories->pluck('id')->toArray());

            DB::commit();
            return $updated;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public function changeApprovalStatus(int $id, $data)
    {
        try {
            DB::beginTransaction();
            $product = $this->model->findOrFail($id);
            $updated = $product->update($data);

            // Invalidate product caches
            $this->invalidateProductCaches(['type' => $product->type], null, $product->categories->pluck('id')->toArray());

            DB::commit();
            return $updated;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public function updatePosition(array $data, int $id)
    {
        try {
            DB::beginTransaction();
            $product = $this->model->findOrFail($id);
            $updated = $product->update($data);

            // Invalidate product caches since position affects ordering
            $this->invalidateProductCaches(['type' => $product->type], null, $product->categories->pluck('id')->toArray());

            DB::commit();
            return $updated;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public function updateProductType(int $id, $data)
    {
        try {
            DB::beginTransaction();
            $product = $this->model->findOrFail($id);
            $oldType = $product->type;
            $updated = $product->update($data);

            // Invalidate product caches for both old and new types
            $this->invalidateProductCaches($data, $oldType, $product->categories->pluck('id')->toArray());

            DB::commit();
            return $updated;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public function deleteOneByAdmin(int $id)
    {
        try {
            DB::beginTransaction();
            $product = $this->model->findOrFail($id);
            $type = $product->type;
            $categoryIds = $product->categories->pluck('id')->toArray();

            $deleted = $product->delete();

            // Invalidate product caches
            $this->invalidateProductCaches(['type' => $type], $type, $categoryIds);

            DB::commit();
            return $deleted;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public function deleteImage(int $id)
    {
        try {
            DB::beginTransaction();
            $image = ProductImage::findOrFail($id);
            $this->deleteFile($image->image);
            $deleted = $image->delete();
            DB::commit();
            return $deleted;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public function addRelatedProducts(array $data)
    {
        try {
            DB::beginTransaction();
            $product = $this->model->findOrFail($data['product_id']);
            $product->relatedProducts()->attach($data['relatedProductIds']);
            DB::commit();
            return $product->relatedProducts;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public function syncRelatedProducts(array $data)
    {
        try {
            DB::beginTransaction();
            $product = $this->model->findOrFail($data['product_id']);
            $product->relatedProducts()->sync($data['relatedProductIds']);
            DB::commit();
            return $product->relatedProducts;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public function removeRelatedProducts($data)
    {
        try {
            DB::beginTransaction();
            $product = $this->model->findOrFail($data['product_id']);
            $product->relatedProducts()->detach($data['relatedProductIds']);
            DB::commit();
            return $product->relatedProducts;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public function getRelatedProducts(Product $product, $limit = 4)
    {
        return $product->relatedProducts()->take($limit)->get();
    }

    public function getPaginatedRelatedProducts(Product $product)
    {
        return $product->relatedProducts()->paginate();
    }

    public function addProductAccessories(array $data)
    {
        try {
            DB::beginTransaction();
            $product = $this->model->findOrFail($data['product_id']);
            $product->accessories()->attach($data['productAccessoriesIds']);
            DB::commit();
            return $product->accessories;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public function syncProductAccessories(array $data)
    {
        try {
            DB::beginTransaction();
            $product = $this->model->findOrFail($data['product_id']);
            $product->accessories()->sync($data['productAccessoriesIds']);
            DB::commit();
            return $product->accessories;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public function removeProductAccessories(array $data)
    {
        try {
            DB::beginTransaction();
            $product = $this->model->findOrFail($data['product_id']);
            $product->accessories()->detach($data['productAccessoriesIds']);
            DB::commit();
            return $product->accessories;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public function getProductAccessories(Product $product, $limit = 4)
    {
        return $product->accessories()->take($limit)->get();
    }

    public function getPaginatedProductAccessories(Product $product)
    {
        return $product->accessories()->paginate();
    }
}