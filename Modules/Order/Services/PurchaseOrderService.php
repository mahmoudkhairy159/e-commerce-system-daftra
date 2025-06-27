<?php

namespace Modules\Order\Services;

use Illuminate\Support\Facades\DB;
use Modules\Cart\Repositories\CartRepository;
use Modules\Order\Enums\OrderStatusEnum;
use Modules\Order\Models\OrderStatusHistory;
use Modules\Order\Repositories\OrderProductRepository;
use Modules\Order\Repositories\OrderRepository;
use Modules\Product\Repositories\ProductRepository;

class PurchaseOrderService
{
    protected $orderRepository;
    protected $cartRepository;
    protected $orderProductRepository;
    protected $productRepository;
    protected $orderValidationService;

    public function __construct(
        OrderRepository $orderRepository,
        CartRepository $cartRepository,
        OrderProductRepository $orderProductRepository,
        ProductRepository $productRepository,
        OrderValidationService $orderValidationService
    ) {
        $this->orderRepository = $orderRepository;
        $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
        $this->orderProductRepository = $orderProductRepository;
        $this->orderValidationService = $orderValidationService;
    }

    public function purchaseOrder(array $data, string $transactionId = null, OrderCalculationService $orderCalculationService)
    {
        DB::beginTransaction();
        try {
            // Step 1: Comprehensive validation before processing
            $validationResult = $this->orderValidationService->validateOrderPlacement($data['user_id']);
            $cart = $validationResult['cart'];

            // Step 2: Validate stock with locking to prevent race conditions
            $productQuantities = $cart->cartProducts->pluck('quantity', 'product_id')->toArray();
            $stockValidation = $this->orderValidationService->validateStockWithLocking($productQuantities);
            $products = $stockValidation['products'];

            // Step 3: Check cart integrity (prices haven't changed)
            $integrityCheck = $this->orderValidationService->validateCartIntegrity($cart);
            if ($integrityCheck['has_issues']) {
                throw new \Exception(__('validation.cart_integrity_issues') . ': ' .
                    json_encode($integrityCheck['issues']), 422);
            }

            // Step 4: Calculate order amounts
            $data = $orderCalculationService->calculateOrderAmount($data);

            // Step 5: Create a new order
            $order = $this->orderRepository->create($data);
            $user = auth()->user();

            // Create initial order status history
            $this->createStatusHistory(
                $order->id,
                null,
                $user,
                OrderStatusEnum::PENDING,
                OrderStatusEnum::PENDING,
                'Order created and validated'
            );

            // Step 6: Move products from cart to order with stock updates
            foreach ($cart->cartProducts as $cartProduct) {
                $product = $products->get($cartProduct->product_id);

                // Double-check stock availability (defensive programming)
                if ($product->stock < $cartProduct->quantity) {
                    throw new \Exception(__('validation.insufficient_stock_during_processing', [
                        'product' => $cartProduct->name,
                        'requested' => $cartProduct->quantity,
                        'available' => $product->stock
                    ]), 422);
                }

                $orderProduct = $this->orderProductRepository->create([
                    'order_id' => $order->id,
                    'product_id' => $cartProduct->product_id,
                    'quantity' => $cartProduct->quantity,
                    'original_price' => $cartProduct->original_price,
                    'discount_amount' => $cartProduct->discount_amount,
                    'price' => $cartProduct->price,
                    'subtotal' => $cartProduct->subtotal,
                    'tax' => $cartProduct->tax,
                ]);

                // Create order product status history
                $this->createStatusHistory(
                    $order->id,
                    $orderProduct->id,
                    $user,
                    OrderStatusEnum::PENDING,
                    OrderStatusEnum::PENDING,
                    'Order product added'
                );

                // Update product stock (using the locked product instance)
                $product->stock -= $cartProduct->quantity;
                $product->save();
            }

            // Step 7: Store transaction details
            if ($transactionId != null) {
                $order->transaction()->create([
                    'user_id' => $data['user_id'],
                    'transaction_id' => $transactionId,
                    'amount' => $order->amount,
                    'payment_status' => $data['payment_status'],
                    'payment_method' => $data['payment_method'],
                    'transaction_details' => 'Transaction initiated for Order #' . $order->id
                ]);
            }

            // Step 8: Empty the cart
            $this->cartRepository->emptyCart($cart->id);

            DB::commit();

            return $order->load('orderProducts');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Create status history with polymorphic relationship
     *
     * @param int $orderId
     * @param int|null $orderProductId
     * @param mixed $changer User, Admin,  model instance
     * @param string|null $statusFrom
     * @param string $statusTo
     * @param string $comment
     * @return void
     */
    private function createStatusHistory(
        int $orderId,
        ?int $orderProductId,
        $changer,
        $statusFrom,
        $statusTo,
        string $comment
    ): void {
        $statusHistory = new OrderStatusHistory([
            'order_id' => $orderId,
            'order_product_id' => $orderProductId,
            'status_from' => $statusFrom,
            'status_to' => $statusTo,
            'comment' => $comment,
        ]);

        // Attach using the polymorphic relationship
        $changer->orderStatusChanges()->save($statusHistory);
    }
}