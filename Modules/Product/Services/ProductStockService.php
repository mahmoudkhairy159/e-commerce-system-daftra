<?php

declare(strict_types=1);

namespace Modules\Product\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Product\Models\Product;
use Modules\Product\Repositories\ProductRepository;

class ProductStockService
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Check if product has sufficient stock
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
            !$product->trashed() &&
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
            $product->approval_status !== Product::APPROVAL_STATUS_APPROVED ||
            $product->trashed()
        ) {
            return 0;
        }

        return $product->stock;
    }

    /**
     * Reserve stock for multiple products (with database locking)
     *
     * @param array $productQuantities Format: ['product_id' => 'quantity']
     * @return array
     * @throws Exception
     */
    public function reserveStock(array $productQuantities): array
    {
        return DB::transaction(function () use ($productQuantities) {
            $productIds = array_keys($productQuantities);

            // Lock products for update to prevent race conditions
            $products = Product::whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $reservationResults = [];
            $stockErrors = [];

            foreach ($productQuantities as $productId => $quantity) {
                $product = $products->get($productId);

                if (!$product) {
                    $stockErrors[$productId] = __('validation.product_not_found');
                    continue;
                }

                // Check product status
                if ($product->status !== Product::STATUS_ACTIVE) {
                    $stockErrors[$productId] = __('validation.product_inactive');
                    continue;
                }

                if ($product->approval_status !== Product::APPROVAL_STATUS_APPROVED) {
                    $stockErrors[$productId] = __('validation.product_not_approved');
                    continue;
                }

                if ($product->trashed()) {
                    $stockErrors[$productId] = __('validation.product_deleted');
                    continue;
                }

                // Check stock availability
                if ($quantity > $product->stock) {
                    $stockErrors[$productId] = __('validation.insufficient_stock', [
                        'requested' => $quantity,
                        'available' => $product->stock
                    ]);
                    continue;
                }

                // Reserve the stock
                $product->stock -= $quantity;
                $product->save();

                $reservationResults[$productId] = [
                    'reserved_quantity' => $quantity,
                    'remaining_stock' => $product->stock,
                    'product_name' => $product->name ?? 'Unknown Product'
                ];
            }

            if (!empty($stockErrors)) {
                throw new Exception(
                    __('validation.stock_reservation_failed'),
                    422,
                    new Exception(json_encode($stockErrors))
                );
            }

            return [
                'reservations' => $reservationResults,
                'total_products' => count($reservationResults),
                'success' => true
            ];
        });
    }

    /**
     * Release reserved stock (useful for order cancellations)
     *
     * @param array $productQuantities Format: ['product_id' => 'quantity']
     * @return array
     * @throws Exception
     */
    public function releaseStock(array $productQuantities): array
    {
        return DB::transaction(function () use ($productQuantities) {
            $productIds = array_keys($productQuantities);

            // Lock products for update
            $products = Product::whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $releaseResults = [];

            foreach ($productQuantities as $productId => $quantity) {
                $product = $products->get($productId);

                if (!$product) {
                    continue; // Skip if product doesn't exist
                }

                // Release the stock
                $product->stock += $quantity;
                $product->save();

                $releaseResults[$productId] = [
                    'released_quantity' => $quantity,
                    'new_stock' => $product->stock,
                    'product_name' => $product->name ?? 'Unknown Product'
                ];
            }

            return [
                'releases' => $releaseResults,
                'total_products' => count($releaseResults),
                'success' => true
            ];
        });
    }

    /**
     * Get stock status for multiple products
     *
     * @param array $productIds
     * @return array
     */
    public function getStockStatus(array $productIds): array
    {
        $products = $this->productRepository->whereIn('id', $productIds)->get();
        $stockStatus = [];

        foreach ($products as $product) {
            $stockStatus[$product->id] = [
                'product_id' => $product->id,
                'name' => $product->name ?? 'Unknown Product',
                'stock' => $product->stock,
                'status' => $product->status,
                'approval_status' => $product->approval_status,
                'is_available' => $this->isProductAvailable($product),
                'is_in_stock' => $product->stock > 0,
                'deleted_at' => $product->deleted_at
            ];
        }

        return $stockStatus;
    }

    /**
     * Check if product is available for purchase
     *
     * @param Product $product
     * @return bool
     */
    protected function isProductAvailable(Product $product): bool
    {
        return $product->status === Product::STATUS_ACTIVE &&
            $product->approval_status === Product::APPROVAL_STATUS_APPROVED &&
            !$product->trashed() &&
            $product->stock > 0;
    }

    /**
     * Update stock for a single product
     *
     * @param int $productId
     * @param int $newStock
     * @return bool
     * @throws Exception
     */
    public function updateStock(int $productId, int $newStock): bool
    {
        if ($newStock < 0) {
            throw new Exception(__('validation.invalid_stock_value'));
        }

        return DB::transaction(function () use ($productId, $newStock) {
            $product = Product::lockForUpdate()->find($productId);

            if (!$product) {
                throw new Exception(__('validation.product_not_found'));
            }

            $product->stock = $newStock;
            return $product->save();
        });
    }

    /**
     * Get low stock products (below minimum threshold)
     *
     * @param int $threshold
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLowStockProducts(int $threshold = 10)
    {
        return Product::where('status', Product::STATUS_ACTIVE)
            ->where('approval_status', Product::APPROVAL_STATUS_APPROVED)
            ->where('stock', '<=', $threshold)
            ->orderBy('stock', 'asc')
            ->get();
    }

    /**
     * Get out of stock products
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOutOfStockProducts()
    {
        return Product::where('status', Product::STATUS_ACTIVE)
            ->where('approval_status', Product::APPROVAL_STATUS_APPROVED)
            ->where('stock', 0)
            ->get();
    }
}
