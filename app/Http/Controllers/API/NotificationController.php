<?php


namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Services\NotificationService;

class NotificationController extends Controller
{
    private $notificationService;
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function getNotification()
    {
        $this->getUser($user);
        $notifications = $this->notificationService->getUserNotification($user->id);

        paging($notifications);
    }

    public function seenNotification($id)
    {
        $this->getUser($user);
        $seen = $this->notificationService->setNotiSeen($user->id, $id);
        if (!$seen) {
            error(messages('Error'));
        }

        success($seen);
    }
}
