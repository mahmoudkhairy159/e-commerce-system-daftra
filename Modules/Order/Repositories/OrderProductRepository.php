<?php

namespace Modules\Order\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Order\Enums\OrderStatusEnum;
use Modules\Order\Models\OrderProduct;
use Modules\Order\Models\OrderStatusHistory;
use Prettus\Repository\Eloquent\BaseRepository;

class OrderProductRepository extends BaseRepository
{
    public function model()
    {
        return OrderProduct::class;
    }
    public function getAll()
    {
        return $this->model
            ->filter(request()->all())
            ->orderBy('created_at', 'desc');
    }

    public function getByOrderId($orderId)
    {
        return $this->model
            ->where('order_id', $orderId)
            ->filter(request()->all())
            ->orderBy('created_at', 'asc');
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