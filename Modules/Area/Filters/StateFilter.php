<?php

namespace Modules\Area\Filters;

use EloquentFilter\ModelFilter;

class StateFilter extends ModelFilter
{


    public function search($search)
    {
        return $this->where(function ($q) use ($search) {
            return $q->whereTranslationLike('name',  "%$search%")->orWhere('code', 'LIKE', "%$search%");
        });
    }
    public function code($code)
    {
        return $this->where(function ($q) use ($code) {
            return $q->where('code', $code);
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
    }
}
