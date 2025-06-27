<?php

declare(strict_types=1);

namespace Modules\Order\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Cart\Models\Cart;
use Modules\Cart\Repositories\CartRepository;
use Modules\Product\Models\Product;
use Modules\Product\Repositories\ProductRepository;

class OrderValidationService
{
    protected $cartRepository;
    protected $productRepository;

    public function __construct(
        CartRepository $cartRepository,
        ProductRepository $productRepository
    ) {
        $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * Validate cart and products for order placement
     *
     * @param int $userId
     * @return array
     * @throws Exception
     */
    public function validateOrderPlacement(int $userId): array
    {
        // Get user's cart with products
        $cart = $this->cartRepository->getCartByUserId($userId);

        if (!$cart || $cart->cartProducts->isEmpty()) {
            throw new Exception(__('validation.cart_empty'));
        }

        $validationErrors = [];
        $productIds = $cart->cartProducts->pluck('product_id')->toArray();

        // Get all products in one query for efficiency
        $products = $this->productRepository->whereIn('id', $productIds)->get()->keyBy('id');

        foreach ($cart->cartProducts as $cartProduct) {
            $product = $products->get($cartProduct->product_id);

            // Validate individual cart product
            $productErrors = $this->validateCartProduct($cartProduct, $product);

            if (!empty($productErrors)) {
                $validationErrors[] = [
                    'cart_product_id' => $cartProduct->id,
                    'product_id' => $cartProduct->product_id,
                    'product_name' => $cartProduct->name,
                    'errors' => $productErrors
                ];
            }
        }

        if (!empty($validationErrors)) {
            throw new Exception(
                __('validation.order_validation_failed'),
                422,
                new Exception(json_encode($validationErrors))
            );
        }

        return [
            'cart' => $cart,
            'products' => $products,
            'total_items' => $cart->cartProducts->sum('quantity'),
            'validation_passed' => true
        ];
    }

    /**
     * Validate individual cart product
     *
     * @param \Modules\Cart\Models\CartProduct $cartProduct
     * @param \Modules\Product\Models\Product|null $product
     * @return array
     */
    protected function validateCartProduct($cartProduct, ?Product $product): array
    {
        $errors = [];

        // Check if product exists
        if (!$product) {
            $errors[] = __('validation.product_not_found');
            return $errors; // Stop further validation if product doesn't exist
        }

        // Check if product is active
        if ($product->status !== Product::STATUS_ACTIVE) {
            $errors[] = __('validation.product_inactive');
        }

        // Check if product is approved
        if ($product->approval_status !== Product::APPROVAL_STATUS_APPROVED) {
            $errors[] = __('validation.product_not_approved');
        }

        // Check if product is soft deleted
        if ($product->trashed()) {
            $errors[] = __('validation.product_deleted');
        }

        // Validate stock availability
        if ($cartProduct->quantity > $product->stock) {
            $errors[] = __('validation.insufficient_stock', [
                'requested' => $cartProduct->quantity,
                'available' => $product->stock
            ]);
        }

        // Validate minimum quantity
        if ($cartProduct->quantity <= 0) {
            $errors[] = __('validation.invalid_quantity');
        }

        // Check if offer is still valid (if using offer price)
        if (
            $product->offer_end_date &&
            $product->offer_end_date < now() &&
            $cartProduct->price == $product->offer_price
        ) {
            $errors[] = __('validation.offer_expired');
        }

        return $errors;
    }

    /**
     * Validate stock availability for multiple products at once
     * This method uses database locking to prevent race conditions
     *
     * @param array $productQuantities Format: ['product_id' => 'quantity']
     * @return array
     * @throws Exception
     */
    public function validateStockWithLocking(array $productQuantities): array
    {
        return DB::transaction(function () use ($productQuantities) {
            $productIds = array_keys($productQuantities);

            // Lock products for update to prevent race conditions
            $products = Product::whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $stockErrors = [];

            foreach ($productQuantities as $productId => $quantity) {
                $product = $products->get($productId);

                if (!$product) {
                    $stockErrors[$productId] = __('validation.product_not_found');
                    continue;
                }

                if ($quantity > $product->stock) {
                    $stockErrors[$productId] = __('validation.insufficient_stock', [
                        'requested' => $quantity,
                        'available' => $product->stock
                    ]);
                }
            }

            if (!empty($stockErrors)) {
                throw new Exception(
                    __('validation.stock_validation_failed'),
                    422,
                    new Exception(json_encode($stockErrors))
                );
            }

            return [
                'products' => $products,
                'validation_passed' => true
            ];
        });
    }

    /**
     * Validate cart integrity - check if cart products match current product data
     *
     * @param Cart $cart
     * @return array
     */
    public function validateCartIntegrity(Cart $cart): array
    {
        $integrityIssues = [];

        foreach ($cart->cartProducts as $cartProduct) {
            $product = $this->productRepository->find($cartProduct->product_id);

            if (!$product) {
                continue;
            }

            // Check if prices have changed significantly
            $currentOfferPrice = $product->offer_price ?? $product->price;

            if (abs($cartProduct->price - $currentOfferPrice) > 0.01) {
                $integrityIssues[] = [
                    'cart_product_id' => $cartProduct->id,
                    'product_id' => $cartProduct->product_id,
                    'issue' => 'price_changed',
                    'cart_price' => $cartProduct->price,
                    'current_price' => $currentOfferPrice,
                    'message' => __('validation.price_changed', [
                        'product' => $cartProduct->name,
                        'old_price' => $cartProduct->price,
                        'new_price' => $currentOfferPrice
                    ])
                ];
            }
        }

        return [
            'issues' => $integrityIssues,
            'has_issues' => !empty($integrityIssues)
        ];
    }

    /**
     * Quick stock check for a single product
     *
     * @param int $productId
     * @param int $quantity
     * @return bool
     */
    public function hasStock(int $productId, int $quantity): bool
    {
        $product = $this->productRepository->find($productId);

        return $product &&
            $product->status === Product::STATUS_ACTIVE &&
            $product->approval_status === Product::APPROVAL_STATUS_APPROVED &&
            $product->stock >= $quantity;
    }

    /**
     * Get available stock for a product
     *
     * @param int $productId
     * @return int
     */
    public function getAvailableStock(int $productId): int
    {
        $product = $this->productRepository->find($productId);

        if (
            !$product ||
            $product->status !== Product::STATUS_ACTIVE ||
            $product->approval_status !== Product::APPROVAL_STATUS_APPROVED
        ) {
            return 0;
        }

        return $product->stock;
    }
}