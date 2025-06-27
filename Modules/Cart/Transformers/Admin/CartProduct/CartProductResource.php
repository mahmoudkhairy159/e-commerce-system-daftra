<?php

namespace Modules\Cart\Transformers\Admin\CartProduct;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Product\Transformers\Admin\Product\ProductResource;

class CartProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'cart_id' => $this->cart_id,
            'product_id' => $this->product_id,
            'name' => $this->name,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'original_price' => $this->original_price,
            'discount_amount' => $this->discount_amount,
            'tax' => $this->tax,
            'subtotal' => $this->subtotal,
            'options' => $this->options,
            'expires_at' => $this->expires_at,
            'product' => new ProductResource($this->whenLoaded('product')), // Load product details if eager-loaded
        ];
    }
}
