<?php

namespace App\Repositories;
use App\Repositories\BaseRepo;
use App\Models\Friend;

class FriendRepo extends BaseRepo
{
    public function getModel()
    {
        return Friend::class;
    }

    public function checkOrCreate($userId, $friendId)
    {
        $friend = $this->model->select('id')->where('user_id', $userId)->where('friend_id', $friendId)->first();
        if (!$friend) {
            $friend = $this->create([
                'user_id' => $userId,
                'friend_id' => $friendId
            ]);
        }

        return $friend;
    }
}
