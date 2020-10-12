<?php


namespace App\Services;


use App\Repositories\FriendRepo;
use App\Repositories\FriendRequestRepo;
use App\Repositories\UserRepo;
use Illuminate\Support\Facades\DB;

class FriendService extends BaseService
{
    public $friendRepo;
    public $friendRequestRepo;
    public $userRepo;
    public function __construct(
        FriendRepo $friendRepo,
        FriendRequestRepo $friendRequestRepo,
        UserRepo $userRepo
    )
    {
        parent::__construct();
        $this->friendRepo = $friendRepo;
        $this->friendRequestRepo = $friendRequestRepo;
        $this->userRepo = $userRepo;
    }

    public function searchFriendByName($userId)
    {
        if ($this->key) {
            return $this->userRepo->model
                    ->select('id', 'first_name', 'last_name', 'avatar_id',
                        DB::raw("CONCAT( last_name, ' ', first_name ) AS name"),
                        DB::raw("0 as sent")
                    )
                    ->with([
                        'Profile',
                        'Avatar' => function($q) {
                            $q->select('id', 'thumbnail');
                        }
                    ])
                    ->whereRaw("users.id not in (select friend.friend_id from friend where friend.user_id = $userId)")
                    ->whereRaw("users.id not in (select friend_request.target_id from friend_request where friend_request.user_id = $userId)")
                    ->where(function ($q) {
                        $q->where('first_name', 'LIKE', "%$this->key%");
                        $q->orWhere('last_name', 'LIKE', "%$this->key%");
                    })
                    ->where("users.id", '!=', $userId)
                    ->orderby('last_name', 'asc')
                    ->limit($this->size)
                    ->get();
        }

        return [];
    }

    public function addFriendRequest($userId, $targetId)
    {
        $request = $this->friendRequestRepo->getModelByFields([
            ['user_id', $userId],
            ['target_id', $targetId]
        ], true);

        if (!$request) {
            $request = $this->friendRequestRepo->create([
                'user_id' => $userId,
                'target_id' => $targetId
            ]);
        }

        return $request;
    }

    public function getUserFriendList($userId)
    {
        return $this->friendRepo->model
            ->with([
                'User' => function($q) {
                    $q->select('id', 'first_name', 'last_name', 'avatar_id',
                        DB::raw("CONCAT( last_name, ' ', first_name ) AS name"));
                },
                'User.Avatar' => function($q) {
                    $q->select('id', 'thumbnail');
                },
                'User.Profile'
            ])
            ->join('users', 'users.id', '=', 'friend.user_id')
            ->where('friend_id', $userId)
            ->orderBy('users.last_name', 'asc')
            ->get();
    }

    public function getFriendDetail($friendId)
    {
        return $this->friendRepo->model
            ->with([
                'User' => function($q) {
                    $q->select('id', 'first_name', 'last_name', 'avatar_id',
                        DB::raw("CONCAT( last_name, ' ', first_name ) AS name"));
                },
                'User.Avatar' => function($q) {
                    $q->select('id', 'thumbnail');
                },
                'User.Profile'
            ])
            ->where('id', $friendId)
            ->first();
    }

    public function acceptFriend($userId, $requestId)
    {
        $request = $this->friendRequestRepo->getModelByFields([
            ['target_id', $userId],
            ['id', $requestId]
        ], true);

        if (!$request) {
            error(messages('NotExist'));
        }

        // Create friend record for current user;
        $this->friendRepo->checkOrCreate($request->target_id, $request->user_id);
        // Create friend record for request user;
        $friend = $this->friendRepo->checkOrCreate($request->user_id, $request->target_id);
        $friend = $this->getFriendDetail($friend->id);

        return $friend;
    }
}
