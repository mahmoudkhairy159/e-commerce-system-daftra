<?php

namespace Modules\Cart\Repositories;

use App\Traits\ProductCalculationTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Cart\Models\Cart;
use Modules\Cart\Models\CartProduct;
use Modules\Product\Models\Product;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class CartRepository
 *
 * Repository for managing shopping cart operations.
 *
 * @package Modules\Cart\Repositories
 */
class CartRepository extends BaseRepository
{
    use ProductCalculationTrait;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Cart::class;
    }

    /**
     * Get the current authenticated user's cart
     *
     * @return Cart
     */
    public function getCart()
    {
        $userId = Auth::guard('user-api')->id();
        return $this->getCartByUserId($userId);
    }

    /**
     * Get a cart by user ID
     *
     * @param int $userId
     * @return Cart
     */
    public function getCartByUserId($userId)
    {
        return $this->model
            ->with('cartProducts')
            ->firstOrCreate(['user_id' => $userId]);
    }

    /**
     * Get cart products for a specific user
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getProducts($userId)
    {
        $cart = $this->getCartByUserId($userId);
        return $cart->cartProducts()
            ->filter(request()->all())
            ->with('product');
    }



    /**
     * Add a product to the cart
     *
     * @param array $data
     * @return CartProduct|bool
     */
    public function addProduct(array $data)
    {
        DB::beginTransaction();

        try {
            // Fetch the cart and product
            $cart = $this->getCart();
            $product = Product::findOrFail($data['product_id']);

            // Check if product is already in the cart
            $existingCartProduct = $cart->cartProducts()
                ->where('product_id', $product->id)
                ->first();

            if ($existingCartProduct) {
                // Update quantity if product already exists in cart
                $newQuantity = $existingCartProduct->quantity + $data['quantity'];
                $this->updateProductCart($existingCartProduct->id, ['quantity' => $newQuantity]);
                DB::commit();
                return true;
            }





            // Calculate discount amount if applicable
            $discountAmount = ($product->price - $product->offer_price) * $data['quantity'];


            // Perform tax and subtotal calculations
            $totalTax = $this->calculateTax($product->offer_price, $data['quantity'], $product->tax_rate);
            $subtotal = $this->calculateSubtotal($product->offer_price, $totalTax, $data['quantity']);

            // Save cart product with discount amount and original price
            $cartProduct = $this->saveCartProduct(
                $product->id,
                $product->name,
                $product->offer_price,
                $totalTax,
                $subtotal,
                $data['quantity'],
                $cart,
                $discountAmount,
                $product->price
            );

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Save a product to the cart
     *
     * @param int $productId
     * @param string $productName
     * @param float $price
     * @param float $totalTax
     * @param float $subtotal
     * @param int $quantity
     * @param Cart $cart
     * @return CartProduct
     */
    private function saveCartProduct($productId, $productName, $price, $totalTax, $subtotal, $quantity, $cart, $discountAmount = 0, $originalPrice = null)
    {
        $expiresAt = request('expires_at', Carbon::now()->addDays(1)->toDateTimeString());

        $cartProduct = new CartProduct([
            'product_id' => $productId,
            'name' => $productName,
            'price' => $price,
            'tax' => $totalTax,
            'subtotal' => $subtotal,
            'quantity' => $quantity,
            'expires_at' => $expiresAt,
            'discount_amount' => $discountAmount,
            'original_price' => $originalPrice ?? $price,
        ]);

        $cart->cartProducts()->save($cartProduct);
        return $cartProduct;
    }

    /**
     * Update a product in the cart
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateProductCart($id, array $data)
    {
        DB::beginTransaction();

        try {
            // Retrieve the cart
            $cart = $this->getCart();

            // Find the product in the cart
            $cartProduct = $cart->cartProducts()->where('id', $id)->firstOrFail();

            // Fetch the product details
            $product = Product::findOrFail($cartProduct->product_id);




            $discountAmount = ($product->price - $product->offer_price) * $data['quantity'];

            // Calculate tax and subtotal based on the applicable price
            $totalTax = $this->calculateTax($product->offer_price, $data['quantity'], $product->tax_rate);
            $subtotal = $this->calculateSubtotal($product->offer_price, $totalTax, $data['quantity']);
            $x = $cartProduct->update([
                'price' => $product->offer_price,
                'tax' => $totalTax,
                'subtotal' => $subtotal,
                'quantity' => $data['quantity'],
                'discount_amount' => $discountAmount,
                'original_price' => $product->price,
                'expires_at' => request('expires_at', Carbon::now()->addDays(1)->toDateTimeString()),
            ]);


            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Remove a product from the cart
     *
     * @param int $id
     * @return bool
     */
    public function removeProduct($id)
    {
        DB::beginTransaction();

        try {
            $cart = $this->getCart();
            $result = $cart->cartProducts()->where('id', $id)->delete();

            if (!$result) {
                throw new Exception("Product not found in cart");
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Empty a cart
     *
     * @param int $cartId
     * @return bool
     */
    public function emptyCart($cartId)
    {
        try {
            DB::beginTransaction();
            $result = CartProduct::where('cart_id', $cartId)->delete();
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Get total items count in a cart
     *
     * @param int|null $userId
     * @return int
     */
    public function getCartItemsCount($userId = null)
    {
        $userId = $userId ?? Auth::guard('user-api')->id();
        $cart = $this->getCartByUserId($userId);

        return $cart->cartProducts()->sum('quantity');
    }


    /**
     * Clear expired items from carts
     *
     * @return int Number of removed items
     */
    public function clearExpiredItems()
    {
        try {
            DB::beginTransaction();
            CartProduct::where('expires_at', '<', Carbon::now())->delete();
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }
    public function getCartSumTotal($userId)
    {
        $cart = $this->getCartByUserId($userId);
        return [
            'price_amount' => $cart->cartProducts()->sum(DB::raw('price * quantity')),
            'original_price_amount' => $cart->cartProducts()->sum(DB::raw('original_price * quantity')),
            'tax_amount' => $cart->cartProducts()->sum('tax'),
            'sum_quantity' => $cart->cartProducts()->sum('quantity'),
            'sum_discount_amount' => $cart->cartProducts()->sum('discount_amount'),
            'sum_subtotal' => $cart->cartProducts()->sum('subtotal'),

        ];
    }
}