<?php

namespace Modules\Wishlist\Transformers\Api\Wishlist;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Wishlist\Transformers\Api\WishlistProduct\WishlistProductResource;

class WishlistResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'items' => WishlistProductResource::collection($this->whenLoaded('wishlistProducts')),
        ];
    }
}
