<?php

namespace App\Http\Traits;

use App\Modules\Authentication\Models\User;
use App\Modules\Portal\Model\Admin;
use App\Repositories\User\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

trait CustomRequest
{
    use CustomResponse;

    /**
     * Check data before process
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function data($request)
    {
        if ($request->getContent()) {
            $input = json_decode($request->getContent(), true);
        } else {
            $input = $request->all();
        }

        //Error on empty input
        if (empty($input)) {
            return false;
        }

        return $input;
    }

    /**
     * Validate api request data
     * @param $input
     * @param $scope
     * @return mixed
     */
    public function validator($input, $scope)
    {
        $validator = Validator::make($input, $scope);

        if ($validator->fails()) {
            $errors = [];
            foreach ($errors as $index => $error) {
                foreach ($error as $mess) {
                    $errors[] = $mess;
                }
            }

            return $errors;
        }

        return false;
    }

    /**
     * Get authenticated user
     * @param $user
     * @param $guard
     */
    public function getUser(&$user, $guard = 'api')
    {
        $auth = Auth::guard($guard)->user();
        $userRepo = new UserRepository();

        if ($auth) {
            $user = $userRepo->getModelById($auth->id);
        }
    }
}