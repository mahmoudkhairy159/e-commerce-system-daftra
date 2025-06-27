<?php

namespace Modules\User\Repositories;

use Modules\User\Models\UserProfile;
use Prettus\Repository\Eloquent\BaseRepository;

class UserProfileRepository extends BaseRepository
{
    public function model()
    {
        return UserProfile::class;
    }
}
