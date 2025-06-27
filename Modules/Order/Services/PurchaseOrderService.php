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

    public function __construct(
        OrderRepository $orderRepository,
        CartRepository $cartRepository,
        OrderProductRepository $orderProductRepository,
        ProductRepository $productRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
        $this->orderProductRepository = $orderProductRepository;
    }

    public function purchaseOrder(array $data, string $transactionId = null, OrderCalculationService $orderCalculationService)
    {
        DB::beginTransaction();
        try {
            // Calculate order amounts
            $data = $orderCalculationService->calculateOrderAmount($data);

            // Create a new order
            $order = $this->orderRepository->create($data);
            $user = auth()->user();
            // Create initial order status history
            $this->createStatusHistory(
                $order->id,
                null,
                $user, // Now passing the user model instance instead of just user_id
                OrderStatusEnum::PENDING, // Initial order has no 'from' status
                OrderStatusEnum::PENDING,
                'Order created'
            );

            $cart = $this->cartRepository->getCartByUserId($data['user_id']);

            // Move products from cart to order
            foreach ($cart->cartProducts as $cartProduct) {
                $product = $this->productRepository->findOrFail($cartProduct->product_id);

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
                    $user, // User model instance
                    OrderStatusEnum::PENDING, // Initial order product has no 'from' status
                    OrderStatusEnum::PENDING,
                    'Order product added'
                );

                // Update product quantity
                $product->stock -= $cartProduct->quantity;
                $product->save();
            }

            // Store transaction details
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

            // Empty the cart
            $this->cartRepository->emptyCart($cart->id);
            DB::commit();

            return $order;
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollBack();
            return false;
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