<?php

namespace Modules\Order\Transformers\Api\OrderProduct;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Order\Transformers\Api\Order\OrderResource;

class OrderProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'order' => new OrderResource($this->whenLoaded('order')),  // Nested OrderResource
            'product' => $this->product ? [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'price' => $this->product->price,
            ] : null,
            'original_price' => $this->original_price,
            'price' => $this->price,
            'discount_amount' => $this->discount_amount,
            'tax' => $this->tax,
            'subtotal' =>  $this->subtotal,
            'quantity' => $this->quantity,
            'status' => $this->status,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

    }
}