<?php

namespace Modules\Product\Transformers\Admin\Product;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Product\Transformers\Admin\ProductImage\ProductImageResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {

        return [
            'id' => $this->id,
            'code' => $this->code,
            'image_url' => $this->image_url,
            'video_url' => $this->video_url,
            'status' => $this->status,
            'position' => $this->position,
            'name' => $this->name,
            'slug' => $this->slug,
            'seo_description' => $this->seo_description,
            'seo_keys' => $this->seo_keys,
            'short_description' => $this->short_description,
            'long_description' => $this->long_description,
            'additional' => $this->additional,
            'return_policy' => $this->return_policy,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'currency' => $this->currency,
            'stock' => $this->stock,
            'price' => $this->price,
            'offer_price' => $this->offer_price,
            'tax_rate' => $this->tax_rate,
            'offer_start_date' => $this->offer_start_date,
            'offer_end_date' => $this->offer_end_date,
            'approval_status' => $this->approval_status,


            'categories' => $this->whenLoaded('categories', function () {
                return $this->categories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                    ];
                });
            }),
            'product_images' => ProductImageResource::collection($this->whenLoaded('productImages')),
            'related_products' => $this->whenLoaded('relatedProducts', function () {
                return $this->relatedProducts->map(function ($relatedProduct) {
                    return [
                        'id' => $relatedProduct->id,
                        'slug' => $relatedProduct->slug,
                        'code' => $relatedProduct->code,
                        'name' => $relatedProduct->name,
                        'image_url' => $relatedProduct->image_url,

                        'currency' =>  $relatedProduct->currency ,
                        'stock' =>$relatedProduct->stock ,
                        'price' => $relatedProduct->price ,
                        'offer_price' =>  $relatedProduct->offer_price ,
                    ];
                });
            }),

            'accessories' => $this->whenLoaded('accessories', function () {
                return $this->accessories->map(function ($accessory) {
                    return [
                        'id' => $accessory->id,
                        'slug' => $accessory->slug,
                        'code' => $accessory->code,
                        'name' => $accessory->name,
                        'image_url' => $accessory->image_url,
                      
                        'currency' =>  $accessory->currency ,
                        'stock' =>$accessory->stock ,
                        'price' => $accessory->price ,
                        'offer_price' =>  $accessory->offer_price ,
                    ];
                });
            }),
            'orders_count' => $this->orders_count,
            'reviews_count' => $this->product_reviews_count,
            'rating' =>  $this->product_reviews_avg_rating ?? '0',
            'translations' => $this->translations,

        ];
    }
}