<?php

namespace App\Repositories;
use App\Repositories\BaseRepo;
use App\Models\Notification;

class NotificationRepo extends BaseRepo
{
    public function getModel()
    {
        return Notification::class;
    }

}
