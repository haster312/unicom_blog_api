<?php


namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function detail(Request $request)
    {
        $user = request()->user();
        $user = $this->userService->userRepo->getModelById($user->id);

        success($user);
    }
}
