<?php

namespace App\Repositories;
use App\Repositories\BaseRepo;
use App\Models\NotificationToken;

class NotificationTokenRepo extends BaseRepo
{
    public function getModel()
    {
        return NotificationToken::class;
    }

}
