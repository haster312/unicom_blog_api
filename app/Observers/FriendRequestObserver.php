<?php

namespace App\Observers;

use App\Models\FriendRequest;
use App\Repositories\NotificationRepo;

class FriendRequestObserver
{
    private $notificationRepo;
    public function __construct(NotificationRepo $notificationRepo)
    {
        $this->notificationRepo = $notificationRepo;
    }

    public function created(FriendRequest $request)
    {
        $data = [
            'user_id' => $request->user_id,
            'target_id' => $request->target_id,
            'request_id' => $request->id,
            'type' => constants('NOTI.FRIEND_REQUEST')
        ];

        $this->notificationRepo->create($data);
    }
}
