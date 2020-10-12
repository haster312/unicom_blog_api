<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use function Symfony\Component\String\u;

class AuthController extends Controller
{
    private $authService;
    private $userService;

    public function __construct(AuthService $authService, UserService $userService)
    {
        $this->authService = $authService;
        $this->userService = $userService;
    }

    /**
     * Register user
     * @param RegisterRequest $request
     */
    public function register(RegisterRequest $request)
    {
        $data = getData($request);
        try {
            $user = $this->userService->createUser($data);

            if (!$user) {
                error(messages('Error'));
            }

            success($user, null, 201);
        } catch (\Exception $exception) {
            error($exception->getMessage());
        }
    }

    public function socialRegister(Request $request)
    {
        $data = getData($request);
        $data['profile'] = [
            'profile_type' => 2
        ];

        try {
            $user = $this->userService->createSocialUser($data);
            if (!$user) {
                error(messages('Error'));
            }

            $user = $this->authService->loginByUser($user);

            success($user);
        } catch (\Exception $exception) {
            error(messages('Error'));
        }
    }

    /**
     * @param LoginRequest $request
     */
    public function login(LoginRequest $request)
    {
        $data = getData($request);
        $user = $this->authService->doLogin($data['email'], $data['password']);
        if (!$user) {
            error(messages('WrongCredential'), 403);
        }

        $detail = $this->userService->getUserDetail($user->id);
        $detail->token = $user->token;

        if (!$detail) {
            error(messages('WrongCredential'), 403);
        }

        success($detail);
    }

    public function checkValidToken()
    {
        $this->getUser($user);

        if (!$user) {
            error(messages('Forbidden'));
        }

        success([
            'id' => $user->id
        ]);
    }

    public function logout()
    {
        if (auth()->check()) {
            auth()->user()->token()->revoke();
            success(true);
        }

        error(messages('Error'), 401);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $this->getUser($user);
        $data = getData($request);

        $changed = $this->authService->changePassword($user->id, $data);
        if (!$changed) {
            error(messages('Error'));
        }

        success($changed);
    }
}
