<?php

namespace Modules\User\Filters;

use EloquentFilter\ModelFilter;

class UserFilter extends ModelFilter
{

    public function search($search)
    {
        return $this->where(function ($q) use ($search) {
            return $q->where('name', 'LIKE', "%$search%")
                ->orWhere('phone', 'LIKE', "%$search%")
                ->orWhere('email', 'LIKE', "%$search%");
        });
    }
    public function status($status)
    {
        return $this->where(function ($q) use ($status) {
            return $q->where('status', $status);
        });
    }
    public function countryId($countryId)
    {
        return $this->whereHas('userAddresses', function ($q) use ($countryId) {
            return $q->where('country_id', $countryId);
        });
    }

    public function cityId($cityId)
    {
        return $this->whereHas('userAddresses', function ($q) use ($cityId) {
            return $q->where('city_id', $cityId);
        });
    }
}
