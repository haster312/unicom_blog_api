<?php


namespace App\Repositories;

use App\Models\User;

class UserRepo extends BaseRepo
{
    public function getModel()
    {
        return User::class;
    }
}
