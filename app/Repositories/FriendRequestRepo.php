<?php

namespace App\Repositories;
use App\Repositories\BaseRepo;
use App\Models\FriendRequest;

class FriendRequestRepo extends BaseRepo
{
    public function getModel()
    {
        return FriendRequest::class;
    }

}
