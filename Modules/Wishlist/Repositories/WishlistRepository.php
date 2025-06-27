<?php

namespace Modules\Wishlist\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Wishlist\Models\Wishlist;
use Modules\Wishlist\Models\WishlistProduct;
use Prettus\Repository\Eloquent\BaseRepository;

class WishlistRepository extends BaseRepository
{
    public function model()
    {
        return Wishlist::class;
    }


    public function getAll()
    {
        return $this->model->with('wishlistProducts');
    }

    public function getWishlistProducts($userId)
    {
        $wishlist = $this->getWishlistByUserId($userId);
        $wishlistProducts =$wishlist->wishlistProducts()
        ->filter(request()->all())
        ->with('product');
        return $wishlistProducts;

    }
  
    public function getWishlistByUserId($userId)
    {
        return $this->model->firstOrCreate(['user_id' => $userId]);
    }

    /**
     * Add a product to the authenticated user's wishlist.
     *
     * @param array $data
     * @return WishlistProduct|bool
     */
    public function addProduct(array $data,$userId)
    {
        DB::beginTransaction();

        try {
            $wishlist = $this->getWishlistByUserId($userId);

            // Check if the product is already in the wishlist
            $existingItem = $wishlist->wishlistProducts()->where('product_id', $data['product_id'])->first();
            if (!$existingItem) {
                $wishlistProduct = new WishlistProduct([
                    'product_id' => $data['product_id'],
                ]);

                $wishlist->wishlistProducts()->save($wishlistProduct);
            }
            DB::commit();
            return true;
        } catch (Exception $e) {
            dd($e->getMessage());
            DB::rollBack();
            return false;
        }
    }

    /**
     * Remove a product from the authenticated user's wishlist.
     *
     * @param int $productId
     * @return bool
     */
    public function removeProduct($id,  $userId)
    {
        DB::beginTransaction();

        try {
            $wishlist = $this->getWishlistByUserId($userId);
            $wishlist->wishlistProducts()->where('id', $id)->delete();

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Empty a specific wishlist by its ID.
     *
     * @param int $wishlistId
     * @return bool
     */
    public function emptyWishlist($wishlistId)
    {
        DB::beginTransaction();

        try {
            WishlistProduct::where('wishlist_id', $wishlistId)->delete();
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }




}