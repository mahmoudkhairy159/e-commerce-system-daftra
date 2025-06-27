<?php

namespace Modules\Product\Transformers\Admin\ProductReview;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [

            'id' => $this->id,
            'product_id' => $this->product_id,
            'user_id' => $this->user_id,
            'comment' => $this->comment,
            'rating' => $this->rating,
            'status' => $this->status,
            'user' => $this->whenLoaded('user', function() {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'image_url' => $this->user->image_url
                ];
            }),
            'product' => $this->whenLoaded('product', function() {
                return [
                    'id' => $this->product->id,
                    'name' => $this->product->name,
                    'image_url' => $this->product->image_url
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

        ];
    }
}
