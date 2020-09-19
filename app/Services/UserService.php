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
        return $this->userRepo->create($data);
    }
}
