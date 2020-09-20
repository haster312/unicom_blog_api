<?php


namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Services\UserService;
use Illuminate\Http\Request;
use function Symfony\Component\String\u;

class UserController extends Controller
{
    public $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function detail(Request $request)
    {
        $this->getUser($user);
        $user = $this->userService->getUserDetail($user->id);

        success($user);
    }

    public function update(UpdateUserRequest $request)
    {
        $this->getUser($user);
        $data = getData($request);

        try {
            $user = $this->userService->updateUserDetail($user->id, $data);

            if (!$user) {
                error(messages('Error'));
            }

            success($user);
        } catch (\Exception $exception) {
            error($exception->getMessage());
        }
    }

    public function changePassword()
    {

    }
}
