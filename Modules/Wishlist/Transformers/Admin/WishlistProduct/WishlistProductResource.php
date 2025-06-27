<?php

namespace Modules\Wishlist\Transformers\Admin\WishlistProduct;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Product\Transformers\Admin\Product\ProductResource;

class WishlistProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'wishlist_id' => $this->wishlist_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'product' => new ProductResource($this->whenLoaded('product')), // Load product details if eager-loaded
        ];
    }
}
