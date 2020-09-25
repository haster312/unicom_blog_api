<?php

namespace App\Repositories;
use App\Repositories\BaseRepo;
use App\Models\UserProfile;

class UserProfileRepo extends BaseRepo
{
    public function getModel()
    {
        return UserProfile::class;
    }

}
