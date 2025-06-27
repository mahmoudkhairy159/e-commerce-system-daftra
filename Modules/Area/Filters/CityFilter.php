<?php

namespace Modules\Area\Filters;

use EloquentFilter\ModelFilter;

class CityFilter extends ModelFilter
{


    public function search($search)
    {
        return $this->where(function ($q) use ($search) {
            return $q->whereTranslationLike('name',  "%$search%");
        });
    }


    public function name($name)
    {
        return $this->where(function ($q) use ($name) {
            return $q->whereTranslationLike('name', "%$name%");
        });
    }
    public function countryId($countryId)
    {
        return $this->where(function ($q) use ($countryId) {
            return $q->where('country_id', $countryId);
        });
    }  public function stateId($stateId)
    {
        return $this->where(function ($q) use ($stateId) {
            return $q->where('state_id', $stateId);
        });
    }
}
