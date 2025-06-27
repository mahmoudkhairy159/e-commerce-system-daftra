<?php

namespace Modules\Order\Transformers\Admin\OrderStatusHistory;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderStatusHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'order_product_id' => $this->order_product_id,
            'status_from' => $this->status_from,
            'status_to' => $this->status_to,
            'comment' => $this->comment,
            'changer' => [
                'id' => $this->changer->id,
                'type' => class_basename($this->changer_type),
                'name' => $this->when($this->changer, function() {
                    return $this->changer->name ??'Unknown';
                }),
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

    }
}
