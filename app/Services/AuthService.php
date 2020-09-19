<?php


namespace App\Services;


use App\Repositories\UserRepo;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    private $userRepo;

    public function __construct(UserRepo $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * log user in with passport
     * @param $email
     * @param $password
     * @return mixed
     */
    public function doLogin($email, $password)
    {
        $checkUser = $this->userRepo->model
            ->where('email', $email)
            ->first();

        if (!$checkUser) {
            return false;
        }

        $credentials = ['email' => $checkUser->email, 'password' => $password . getHash()];

        $user = auth()->attempt($credentials);

        if (!$user) {
            return false;
        }

        return $this->getToken();
    }

    /**
     * Login by user model
     * @param $user
     * @return bool|\Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function loginByUser($user)
    {
        auth()->login($user);

        return $this->getToken();
    }

    public function getToken()
    {
        $user = auth()->user();

        $token = $user->createToken(constants('APP_NAME'));

        if ($token) {
            $loggedUser = Auth::user();
            $loggedUser->token = $token->accessToken;
            $loggedUser->expires_at = strtotime($token->token->expires_at);

            auth()->setUser($loggedUser);
            unset($loggedUser->tokens);

            return $loggedUser;
        }

        return false;
    }
}
