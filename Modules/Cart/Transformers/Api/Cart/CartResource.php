<?php

namespace Modules\Cart\Transformers\Api\Cart;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Cart\Transformers\Api\CartProduct\CartProductResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'total_price' => $this->cartProducts()->count() == 0 ? $this->cartProducts->sum(function ($cartProduct) {
                return $cartProduct->product->price * $cartProduct->quantity;
            }) : 0,
            // 'discount_amount' => $this->discount_amount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'items' => CartProductResource::collection($this->whenLoaded('cartProducts')),
        ];
    }
}
