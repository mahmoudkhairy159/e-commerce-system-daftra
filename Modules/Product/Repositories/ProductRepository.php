<?php

namespace Modules\Product\Repositories;

use App\Traits\SoftDeletableTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Product\Enums\ProductTypeEnum;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductImage;
use Prettus\Repository\Eloquent\BaseRepository;

class ProductRepository extends BaseRepository
{
    use SoftDeletableTrait;
    public function model()
    {
        return Product::class;
    }
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


    public function getFavoriteCustomersCountByProductId($id)
    {
        // return [
        //     'no_of_customers' => Wishlist::where('user_id', auth()->id())
        //         ->whereHas('items', function ($query) use ($id) {
        //             $query->where('product_id', $id);
        //         })
        //         ->count(),
        // ];
        return null;
    }
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
            DB::commit();

            return $created;
        } catch (\Throwable $th) {
            dd($th->getMessage());

            DB::rollBack();
            return false;
        }
    }


    public function updateOneByAdmin(array $data, int $id)
    {
        try {
            DB::beginTransaction();
            $product = $this->model->findOrFail($id);
            if (request()->hasFile('image')) {
                if ($product->image) {
                    $this->deleteFile($product->image);
                }
                $data['image'] = $this->uploadFile(request()->file('image'), Product::FILES_DIRECTORY);
            }
            $updated = $product->update($data);
            $data['product_id'] = $product->id;
            $product->categories()->syncWithoutDetaching($data['categoryIds']);
            if (isset($data['relatedProductIds']) && is_array($data['relatedProductIds'])) {
                $this->syncRelatedProducts($data);
            }
            if (isset($data['productAccessoriesIds']) && is_array($data['productAccessoriesIds'])) {
                $this->syncProductAccessories($data);
            }
            DB::commit();

            return $updated;
        } catch (\Throwable $th) {

            DB::rollBack();
            return false;
        }
    }
       public function changeStatus(int $id)
    {
        try {
            DB::beginTransaction();
            $product = $this->model->findOrFail($id);
            $product->status = $product->status == Product::STATUS_ACTIVE ? Product::STATUS_INACTIVE : Product::STATUS_ACTIVE;
            $updated = $product->save();
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
            $model = $this->model->findOrFail($id);
            $updated = $model->update($data);
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
            $updated = $product->update($data);
            DB::commit();

            return $updated;
        } catch (\Throwable $th) {

            DB::rollBack();
            return false;
        }
    }
    //delete by product
    public function deleteOneByAdmin(int $id)
    {
        try {
            DB::beginTransaction();

            $product = $this->model->findOrFail($id);
            // if ($product->image) {
            //     $this->deleteFile($product->image);
            // }
            $deleted = $product->delete();

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

            $product = $this->model->findOrFail($id);
            if ($product->image) {
                $this->deleteFile($product->image);
                $product->image = null;
                $product->save();
            }
            DB::commit();
            return true;
        } catch (\Throwable $th) {

            DB::rollBack();
            return false;
        }
    }

    /*********************************Related_products***************************************/
    public function addRelatedProducts(array $data)
    {
        try {
            DB::beginTransaction();
            $product = $this->model->findOrFail($data['product_id']);
            $product->relatedProducts()->syncWithoutDetaching($data['relatedProductIds']);

            // Ensure bidirectional relationship
            foreach ($data['relatedProductIds'] as $relatedProductId) {
                $relatedProduct = $this->model->findOrFail($relatedProductId);
                $relatedProduct->relatedProducts()->syncWithoutDetaching($data['product_id']);
            }
            DB::commit();
            return true;
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

            // Detach all old related products
            $currentRelatedProducts = $product->relatedProducts()->pluck('related_product_id')->toArray();
            foreach ($currentRelatedProducts as $relatedProductId) {
                $relatedProduct = $this->model->findOrFail($relatedProductId);
                $relatedProduct->relatedProducts()->detach($data['product_id']);
            }

            // Sync the new related products
            $product->relatedProducts()->sync($data['relatedProductIds']);

            // Ensure bidirectional relationship
            foreach ($data['relatedProductIds'] as $relatedProductId) {
                $relatedProduct = $this->model->findOrFail($relatedProductId);
                $relatedProduct->relatedProducts()->syncWithoutDetaching($data['product_id']);
            }

            DB::commit();
            return true;
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
            // Ensure bidirectional relationship
            foreach ($data['relatedProductIds'] as $relatedProductId) {
                $relatedProduct = $this->model->findOrFail($relatedProductId);
                $relatedProduct->relatedProducts()->detach($data['product_id']);
            }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public function getRelatedProducts(Product $product, $limit = 4)
    {
        return $product->relatedProducts()->inRandomOrder()->limit($limit);
    }
    public function getPaginatedRelatedProducts(Product $product)
    {
        return $product->relatedProducts();
    }
    /*********************************Related_products***************************************/


    /*********************************product_accessories***************************************/
    public function addProductAccessories(array $data)
    {
        try {
            DB::beginTransaction();
            $product = $this->model->findOrFail($data['product_id']);
            $product->accessories()->syncWithoutDetaching($data['ProductAccessoriesIds']);

            // Ensure bidirectional relationship
            foreach ($data['ProductAccessoriesIds'] as $productAccessoryId) {
                $productAccessory = $this->model->findOrFail($productAccessoryId);
                $productAccessory->accessories()->syncWithoutDetaching($data['product_id']);
            }
            DB::commit();
            return true;
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

            // Detach all old product accessories
            $currentProductAccessories = $product->accessories()->pluck('accessory_id')->toArray();
            foreach ($currentProductAccessories as $productAccessoryId) {
                $productAccessory = $this->model->findOrFail($productAccessoryId);
                $productAccessory->accessories()->detach($data['product_id']);
            }

            // Sync the new related products
            $product->accessories()->sync($data['productAccessoriesIds']);

            // Ensure bidirectional relationship
            foreach ($data['productAccessoriesIds'] as $productAccessoryId) {
                $productAccessory = $this->model->findOrFail($productAccessoryId);
                $productAccessory->accessories()->syncWithoutDetaching($data['product_id']);
            }

            DB::commit();
            return true;
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

            // Ensure bidirectional relationship
            foreach ($data['productAccessoriesIds'] as $productAccessoryId) {
                $productAccessory = $this->model->findOrFail($productAccessoryId);
                $productAccessory->accessories()->detach($data['product_id']);
            }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public function getProductAccessories(Product $product, $limit = 4)
    {
        return $product->accessories()->inRandomOrder()->limit($limit);
    }
    public function getPaginatedProductAccessories(Product $product)
    {
        return $product->accessories();
    }
    /*********************************product_accessories***************************************/

}