<?php


namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Services\FriendService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class FriendController extends Controller
{
    private $friendService;
    private $notificationService;

    public function __construct(FriendService $friendService, NotificationService $notificationService)
    {
        $this->friendService = $friendService;
        $this->notificationService = $notificationService;
    }

    public function getFriendList()
    {
        $this->getUser($user);
        $friends = $this->friendService->getUserFriendList($user->id);
        success($friends);
    }

    public function searchFriend()
    {
        $this->getUser($user);
        $friends = $this->friendService->searchFriendByName($user->id);
        success($friends);
    }

    public function addRequest(Request $request)
    {
        $this->getUser($user);
        $data = getData($request);
        if (!isset($data['user_id'])) {
            error(messages('Error'));
        }

        $friendRequest = $this->friendService->addFriendRequest($user->id, $data['user_id']);
        success($friendRequest);
    }

    public function acceptRequest(Request $request)
    {
        $this->getUser($user);
        $data = getData($request);

        if (!isset($data['request_id'])) {
            error(messages('Error'));
        }

        try {
            $friend = $this->friendService->acceptFriend($user->id, $data['request_id']);
            if ($friend) {
                $this->notificationService->setNotiSeen($user->id, $data['id']);
            }

            success($friend);
        } catch (\Exception $exception) {
            error($exception->getMessage());
        }
    }

    public function rejectRequest(Request $request)
    {
        $this->getUser($user);
    }
}
