<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\Role;
use Prettus\Repository\Eloquent\BaseRepository;

class RoleRepository extends BaseRepository
{
    public function model()
    {
        return Role::class;
    }
}
