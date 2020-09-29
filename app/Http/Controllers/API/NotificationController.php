<?php


namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;

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

        success($notifications);
    }

    public function getLatestNotification()
    {
        $this->getUser($user);
        $notification = $this->notificationService->getLatestNotification($user->id);
        success($notification);
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

    public function setNotificationToken(Request $request)
    {
        try {
            $this->getUser($user);
            $data = getData($request);

            $token = $this->notificationService->addUserNotificationToken($user->id, $data['token']);
            if (!$token) {
                error(messages('Error'));
            }

            success($token);
        } catch (\Exception $exception) {
            error(messages('Error'));
        }
    }
}
