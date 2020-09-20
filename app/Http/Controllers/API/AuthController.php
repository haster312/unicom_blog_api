<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use App\Services\UserService;
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


    /**
     * @param LoginRequest $request
     */
    public function login(LoginRequest $request)
    {
        $data = getData($request);
        $user = $this->authService->doLogin($data['email'], $data['password']);

        if (!$user) {
            error(messages('WrongCredential'), 401);
        }

        success($user);
    }

    public function logout()
    {
        if (auth()->check()) {
            auth()->user()->AuthAcessToken()->delete();
            success(true);
        }

        error(false);
    }
}
