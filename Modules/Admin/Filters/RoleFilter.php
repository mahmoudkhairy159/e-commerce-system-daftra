<?php

namespace Modules\Admin\Filters;

use EloquentFilter\ModelFilter;

class RoleFilter extends ModelFilter
{


    public function search($search)
    {
        return $this->where(function ($q) use ($search) {
            return $q->where('name', 'LIKE', "%$search%");
        });
    }
}
