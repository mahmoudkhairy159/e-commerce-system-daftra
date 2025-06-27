<?php

namespace Modules\Cart\Filters;

use EloquentFilter\ModelFilter;

class CartProductFilter extends ModelFilter
{
    public function search($search)
    {
        return $this->whereHas('product', function ($q) use ($search) {
            $q->whereTranslationLike('name', "%$search%")
                ->orWhereTranslationLike('short_description', "%$search%")
                ->orWhereTranslationLike('long_description', "%$search%")
                ->orWhere('code', 'LIKE', "%$search%");
        });
    }

    public function cartId($cartId)
    {
        return $this->where('cart_id', $cartId);
    }

    public function productId($productId)
    {
        return $this->where('product_id', $productId);
    }

    public function name($name)
    {
        return $this->where('name', 'LIKE', "%$name%");
    }

    public function quantity($quantity)
    {
        return $this->where('quantity', $quantity);
    }

    public function price($price)
    {
        return $this->where('price', $price);
    }

    public function tax($tax)
    {
        return $this->where('tax', $tax);
    }

    public function subtotal($subtotal)
    {
        return $this->where('subtotal', $subtotal);
    }



    public function expiresAt($expiresAt)
    {
        return $this->whereDate('expires_at', $expiresAt);
    }



    public function status($status)
    {
        return $this->whereHas('product', function ($q) use ($status) {
            $q->where('status', $status);
        });
    }

    public function type($type)
    {
        return $this->whereHas('product', function ($q) use ($type) {
            $q->where('type', $type);
        });
    }

    public function categoryName($categoryName)
    {
        return $this->whereHas('product', function ($q) use ($categoryName) {
            $q->whereHas('categories', function ($q) use ($categoryName) {
                $q->whereTranslationLike('name', "%$categoryName%");
            });
        });
    }

  

    public function latest()
    {
        return $this->orderBy('created_at', 'DESC');
    }

    public function offers($offers)
    {
        if ($offers) {
            return $this->whereHas('product', function ($q) {
                $q->where('offer_price', '>', 0)
                    ->whereDate('offer_start_date', '<=', now())
                    ->whereDate('offer_end_date', '>=', now());
            });
        }
        return $this;
    }

    public function fromPrice($fromPrice)
    {
        return $this->whereHas('product', function ($q) use ($fromPrice) {
            $q->where('price', '>=', $fromPrice);
        });
    }

    public function toPrice($toPrice)
    {
        return $this->whereHas('product', function ($q) use ($toPrice) {
            $q->where('price', '<=', $toPrice);
        });
    }

}