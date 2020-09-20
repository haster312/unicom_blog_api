<?php


namespace App\Services;


use App\Repositories\UserRepo;

class UserService
{
    public $userRepo;

    public function __construct(UserRepo $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * Create new user
     * @param $data
     * @return mixed
     */
    public function createUser($data)
    {
        if (!isset($data['username'])) {
            $data['username'] = $data['last_name'] . $data['first_name'];
        }

        return $this->userRepo->create($data);
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
                            }
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
