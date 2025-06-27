<?php

namespace Modules\Order\Transformers\Admin\Order;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Order\Transformers\Admin\OrderProduct\OrderProductResource;
use Modules\Order\Transformers\Admin\OrderStatusHistory\OrderStatusHistoryResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,

            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ] : null,

            //'transaction' => new TransactionResource($this->whenLoaded('transaction')),

            'total_amount' => $this->total_amount,
            'tax_amount' => $this->tax_amount,
            'shipping_amount' => $this->shipping_amount,
            'discount_amount' => $this->discount_amount,

            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,

            'order_address' => $this->order_address,
            'shipping_method' => $this->shipping_method,
            'coupon' => $this->coupon,
            'notes' => $this->notes,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'order_products' => OrderProductResource::collection($this->whenLoaded('orderProducts')),
            'status_histories' => OrderStatusHistoryResource::collection($this->whenLoaded('statusHistories')),
        ];

    }
}
