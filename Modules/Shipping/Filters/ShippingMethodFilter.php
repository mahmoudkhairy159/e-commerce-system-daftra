<?php

namespace Modules\Shipping\Filters;

use EloquentFilter\ModelFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ShippingMethodFilter extends ModelFilter
{

    public function search($search)
    {
        return $this->where(function ($q) use ($search) {
            return $q->whereTranslationLike('title', "%$search%")
                ->OrWhereTranslationLike('description', "%$search%");
        });
    }


    public function status($status)
    {
        return $this->where('status', $status);
    }


}
