<?php
namespace Modules\Product\Filters;

use EloquentFilter\ModelFilter;
use Carbon\Carbon;

class ProductFilter extends ModelFilter
{
    /**
     * Search across product fields
     */
    public function search($search)
    {
        return $this->where(function ($q) use ($search) {
            return $q->whereTranslationLike('name', "%$search%")
                ->orWhereTranslationLike('short_description', "%$search%")
                ->orWhereTranslationLike('long_description', "%$search%")
                ->orWhere('code', 'LIKE', "%$search%");
        });
    }

    /**
     * Filter by exact product code
     */
    public function code($code)
    {
        return $this->where('code', $code);
    }






    public function categoryIds($categoryIds)
    {
        return $this->whereHas('categories', function ($q) use ($categoryIds) {
            $q->whereIn('categories.id', $categoryIds);
        });
    }




    /**
     * Filter by product status
     */
    public function status($status)
    {
        return $this->where('status', $status);
    }

    /**
     * Filter by product type
     */
    public function type($type)
    {
        return $this->where('type', $type);
    }

    /**
     * Filter by exact creation date
     */
    public function createdAt($createdAt)
    {
        return $this->whereDate('created_at', Carbon::parse($createdAt));
    }

    /**
     * Filter by category name
     */
    public function categoryName($categoryName)
    {
        return $this->whereHas('categories', function ($q) use ($categoryName) {
            $q->whereTranslationLike('name', "%$categoryName%");
        });
    }



    /**
     * Order by latest creation date
     */
    public function latest()
    {
        return $this->orderBy('created_at', 'DESC');
    }

    /**
     * Order by position
     */
    public function position()
    {
        return $this->orderBy('position', 'asc');
    }

    /**
     * Filter active offers
     */
    public function offers($offers)
    {
        if ($offers) {
            return $this->where('offer_price', '>', 0)
                ->whereDate('offer_start_date', '<=', now())
                ->whereDate('offer_end_date', '>=', now());
        }
        return $this;
    }

    /**
     * Filter by minimum price
     */
    public function fromPrice($fromPrice)
    {
        return $this->where('offer_price', '>=', $fromPrice);
    }

    /**
     * Filter by maximum price
     */
    public function toPrice($toPrice)
    {
        return $this->where('offer_price', '<=', $toPrice);
    }







    /**
     * Date range filter
     */
    public function dateFrom($date)
    {
        return $this->whereDate('created_at', '>=', Carbon::parse($date));
    }

    /**
     * Date range filter
     */
    public function dateTo($date)
    {
        return $this->whereDate('created_at', '<=', Carbon::parse($date));
    }
}