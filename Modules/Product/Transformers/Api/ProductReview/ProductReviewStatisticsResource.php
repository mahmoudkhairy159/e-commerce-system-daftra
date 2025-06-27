<?php

namespace Modules\Product\Transformers\Api\ProductReview;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductReviewStatisticsResource  extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {


            return [
                'average_rating' => $this['average_rating'],
                'total_reviews' => $this['total_reviews'],
                'star_counts' => [
                    '5_star' => $this['star_counts'][5],
                    '4_star' => $this['star_counts'][4],
                    '3_star' => $this['star_counts'][3],
                    '2_star' => $this['star_counts'][2],
                    '1_star' => $this['star_counts'][1],
                ]
            ];


    }
}
