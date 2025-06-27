<?php

namespace Modules\Product\Filters;


use EloquentFilter\ModelFilter;

class ProductReviewFilter extends ModelFilter
{

    public function search($search)
    {
        return $this->where(function ($q) use ($search) {
            return $q->where('comment', 'LIKE', "%$search%");
        });
    }
    public function rating($rating)
    {
        return $this->where(function ($q) use ($rating) {
            return $q->where('rating', $rating);
        });
    }
    public function productId($productId)
    {
        return $this->where(function ($q) use ($productId) {
            return $q->where('product_id', $productId);
        });
    }

    public function userId($userId)
    {
        return $this->where(function ($q) use ($userId) {
            return $q->where('user_id', $userId);
        });
    }

    public function status($status)
    {
        return $this->where(function ($q) use ($status) {
            return $q->where('status', $status);
        });
    }

}
