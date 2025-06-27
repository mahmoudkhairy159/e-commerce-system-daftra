<?php

namespace Modules\Order\Repositories;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Order\Enums\OrderStatusEnum;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderStatusHistory;
use Modules\User\Models\User;
use Prettus\Repository\Eloquent\BaseRepository;

class OrderRepository extends BaseRepository
{
    public function model()
    {
        return Order::class;
    }
    public function getAll()
    {
        return $this->model
            ->filter(request()->all())
            ->with(['orderProducts.product', 'orderProducts'])
            ->orderBy('created_at', 'desc');
    }

    public function getAllByStatus(string $status)
    {
        return $this->model
            ->with(['orderProducts.product', 'orderProducts'])
            ->filter(request()->all())
            ->where('status', $status)
            ->orderBy('created_at', 'desc');
    }



    public function getByUserId(int $userId)
    {
        return $this->model
            ->where('user_id', $userId)
            ->with(['orderProducts.product', 'orderProducts'])
            ->filter(request()->all())
            ->orderBy('created_at', 'desc');
    }
    public function getOneForUser(int $id)
    {
        $userId = Auth::id();
        return $this->model
            ->where('id', $id)
            ->where('user_id', $userId)
            ->with(['orderProducts.product', 'orderProducts','statusHistories'])
            ->first();
    }
 
    public function getOneById(int $id)
    {
        return $this->model
            ->where('id', $id)
            ->with(['orderProducts.product', 'orderProducts','statusHistories'])
            ->first();
    }
    public function updateStatus(array $data, int $id)
    {
        try {
            DB::beginTransaction();
            $order = $this->model->findOrFail($id);
            $statusFrom = $order->status;
            $order->status = $data['status'];
            $updated = $order->save();

            if ($updated) {
                // Create status history entry
                $admin = auth()->user(); // Get the current authenticated admin
                $comment = $data['comment'] ?? 'Status changed from ' . $statusFrom . ' to ' . $data['status'] . 'by admin';

                $this->createStatusHistory(
                    $id,                // order_id
                    null,               // order_product_id (null as we're updating the whole order)
                    $admin,            // user_id
                    $statusFrom,        // status_from
                    $data['status'],    // status_to
                    $comment            // comment
                );
            }
            DB::commit();
            return $updated;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }
    public function deleteOne(int $id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $user= auth()->user();
                $order = $this->model
                    ->with(['orderProducts.product'])
                    ->findOrFail($id);

                // Check if order is already cancelled
                if ($order->status === OrderStatusEnum::CANCELLED) {
                    return true;
                }

                // Validate all order products can be cancelled
                $this->validateOrderCanBeCancelled($order);

                // Process the cancellation
                $this->processCancellation($order, $user);

                return true;
            });
        } catch (\Throwable $th) {

            DB::rollBack();
            return false;
        }
    }









    public function deleteOneByUser(int $id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $user = auth()->user();
                $order = $this->model
                    ->with(['orderProducts.product'])
                    ->where('user_id', $user->id)
                    ->findOrFail($id);

                // Check if order is already cancelled
                if ($order->status === OrderStatusEnum::CANCELLED) {
                    return true;
                }

                // Validate all order products can be cancelled
                $this->validateOrderCanBeCancelled($order);

                // Process the cancellation
                $this->processCancellation($order, $user);

                return true;
            });
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function validateOrderCanBeCancelled($order): void
    {
        foreach ($order->orderProducts as $orderProduct) {
            if (!$orderProduct->status->isActive()) {
                throw new Exception(
                    "Cannot cancel order with items in {$orderProduct->status->label()} status"
                );
            }
        }
    }

    /**
     * Process the cancellation of an order and its products
     *
     * @param Order $order The order to cancel
     * @param int $userId The ID of the user performing the cancellation
     */
    private function processCancellation($order, User $user): void
    {
        // Update order status and track the old status
        $oldOrderStatus = $order->status;
        $order->status = OrderStatusEnum::CANCELLED;
        $order->save();

        // Create order status history
        $this->createStatusHistory(
            $order->id,
            null,
            $user,
            $oldOrderStatus,
            OrderStatusEnum::CANCELLED,
            'Order cancelled by customer'
        );

        // Cancel each order product and restore stock
        foreach ($order->orderProducts as $orderProduct) {
            $oldProductStatus = $orderProduct->status;
            $orderProduct->status = OrderStatusEnum::CANCELLED;
            $orderProduct->save();

            // Restore product stock
            $product = $orderProduct->product;
            $product->stock += $orderProduct->quantity;
            $product->save();

            // Create status history for the product
            $this->createStatusHistory(
                $order->id,
                $orderProduct->id,
                $user,
                $oldProductStatus->value,
                OrderStatusEnum::CANCELLED,
                'Order cancelled by customer'
            );
        }
    }


    private function createStatusHistory(
        int $orderId,
        ?int $orderProductId,
        $changer,
        $statusFrom,
        $statusTo,
        string $comment
    ): void {
        // Create the history record with the polymorphic relationship
        $statusHistory = new OrderStatusHistory([
            'order_id' => $orderId,
            'order_product_id' => $orderProductId,
            'status_from' => $statusFrom,
            'status_to' => $statusTo,
            'comment' => $comment,
        ]);

        // Attach the polymorphic relationship
        $changer->orderStatusChanges()->save($statusHistory);
    }



}