<?php

namespace Modules\Order\Filters;

use EloquentFilter\ModelFilter;

class OrderFilter extends ModelFilter
{
    /**
     * Filter by user_id.
     *
     * @param int $userId
     * @return $this
     */
    public function userId($userId)
    {
        return $this->where('user_id', $userId);
    }





    /**
     * Filter by status.
     *
     * @param string $status
     * @return $this
     */
    public function status($status)
    {
        return $this->where('status', $status);
    }

    /**
     * Filter by payment_method.
     *
     * @param string $paymentMethod
     * @return $this
     */
    public function paymentMethod($paymentMethod)
    {
        return $this->where('payment_method', $paymentMethod);
    }
    public function paymentStatus($paymentStatus)
    {
        return $this->where('payment_status', $paymentStatus);
    }

    /**
     * Filter by total_price range.
     *
     * @param float $minTotalCost
     * @param float $maxTotalCost
     * @return $this
     */
    public function fromSubTotal($fromSubTotal)
    {
        return $this->where(function ($q) use ($fromSubTotal) {
            return $q->where('sub_total', '>=', $fromSubTotal);
        });
    }

    public function toSubTotal($toSubTotal)
    {
        return $this->where(function ($q) use ($toSubTotal) {
            return $q->where('sub_total', '<=', $toSubTotal);
        });
    }



    /**
     * Filter by notes (search).
     *
     * @param string $notes
     * @return $this
     */
    public function notes($notes)
    {
        return $this->where('notes', 'like', "%$notes%");
    }

}
