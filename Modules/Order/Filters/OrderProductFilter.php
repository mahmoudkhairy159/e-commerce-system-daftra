<?php

namespace Modules\Order\Filters;

use EloquentFilter\ModelFilter;

class OrderProductFilter extends ModelFilter
{



    /**
     * Filter by product_id.
     *
     * @param int $itemId
     * @return $this
     */
    public function productId($productId)
    {
        return $this->where('product_id', $productId);
    }
    /**
     * Filter by orderId.
     *
     * @param int $orderId
     * @return $this
     */
    public function orderId($orderId)
    {
        return $this->where('order_id', $orderId);
    }
  

    /**
     * Filter by stock.
     *
     * @param int $quantity
     * @return $this
     */
    public function quantity($quantity)
    {
        return $this->where('quantity', $quantity);
    }

    /**
     * Filter by price.
     *
     * @param float $price
     * @return $this
     */
    public function unitPrice($unitPrice)
    {
        return $this->where('price', $unitPrice);
    }

}