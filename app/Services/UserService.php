<?php


namespace App\Services;


use App\Repositories\UserProfileRepo;
use App\Repositories\UserRepo;

class UserService
{
    public $userRepo;
    public $userProfileRepo;

    public function __construct(UserRepo $userRepo, UserProfileRepo $userProfileRepo)
    {
        $this->userRepo = $userRepo;
        $this->userProfileRepo = $userProfileRepo;
    }

    /**
     * Create new user
     * @param $data
     * @return mixed
     */
    public function createUser($data)
    {
        $profile = $data['profile'];
        unset($data['profile']);

        $user = $this->userRepo->create($data);
        if (!$user) {
            return false;
        }

        $profile['user_id'] = $user->id;

        $profile = $this->userProfileRepo->create($profile);

        return $this->getUserDetail($user->id);
    }

    public function getUserDetail($userId)
    {
        return $this->userRepo->model
                        ->with([
                            'Avatar' => function($q) {
                                $q->select('id', 'main', 'thumbnail');
                            },
                            'University' => function($q) {
                                $q->select('id', 'name');
                            },
                            'Course' => function($q) {
                                $q->select('id', 'name');
                            },
                            'Profile'
                        ])
                        ->where('id', $userId)
                        ->first();
    }

    public function updateUserDetail($userId, $data)
    {
        $user = $this->getUserDetail($userId);
        $user->update($data);

        return $user;
    }
}
