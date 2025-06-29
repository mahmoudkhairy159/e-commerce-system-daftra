<?php

namespace Modules\User\Filters;

use EloquentFilter\ModelFilter;

class UserAddressFilter extends ModelFilter
{
     /**
     * Search across multiple fields.
     */
    public function search($search)
    {
        return $this->where(function ($query) use ($search) {
            $query->where('address', 'LIKE', "%$search%")
                  ->orWhere('zip_code', 'LIKE', "%$search%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'LIKE', "%$search%");
                  });
        });
    }
    /**
     * Filter by user_id.
     */
    public function userId($userId)
    {
        return $this->where('user_id', $userId);
    }

    /**
     * Filter by zip_code.
     */
    public function zipCode($zipCode)
    {
        return $this->where('zip_code', 'LIKE', "%$zipCode%");
    }

    /**
     * Filter by address.
     */
    public function address($address)
    {
        return $this->where('address', 'LIKE', "%$address%");
    }

    /**
     * Filter by type.
     */
    public function type($type)
    {
        return $this->where('type', $type);
    }


}