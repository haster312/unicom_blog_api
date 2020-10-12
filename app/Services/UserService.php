<?php


namespace App\Services;


use App\Repositories\ImageRepo;
use App\Repositories\UserProfileRepo;
use App\Repositories\UserRepo;

class UserService
{
    public $userRepo;
    public $userProfileRepo;
    public $imageRepo;

    public function __construct(
        UserRepo $userRepo,
        UserProfileRepo $userProfileRepo,
        ImageRepo $imageRepo
    )
    {
        $this->userRepo = $userRepo;
        $this->userProfileRepo = $userProfileRepo;
        $this->imageRepo = $imageRepo;
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

    public function createSocialUser($data)
    {
        $socialId = $data['social_id'];
        $socialType = $data['social'];

        $user = $this->userRepo->getModelByFields([
            ['social_id', $socialId],
            ['social', $socialType]
        ], true);

        if (!$user) {
            $profile = $data['profile'];
            $username = strtolower(trim($data['last_name']) . trim($data['first_name'])) . "10";
            $this->checkUsername($username);

            $data['username'] = $username;

            unset($data['profile']);

            $user = $this->userRepo->create($data);
            if (!$user) {
                return false;
            }

            $profile['user_id'] = $user->id;
            $profile['profile_type'] = 2;
            $this->userProfileRepo->create($profile);
            if ($data['image_url']) {
                $image = $this->imageRepo->create([
                    'main' => $data['image_url'],
                    'thumbnail' => $data['image_url']
                ]);

                $data['avatar_id'] = $image->id;
            }

            $user = $this->userRepo->update($user->id, $data);
        }

        return $this->getUserDetail($user->id);
    }

    public function getUserDetail($userId)
    {
        return $this->userRepo->model
                        ->with([
                            'Avatar' => function($q) {
                                $q->select('id', 'main', 'thumbnail');
                            },
                            'Profile'
                        ])
                        ->where('id', $userId)
                        ->first();
    }

    public function getUserByUsername($username)
    {
        return $this->userRepo->model
            ->select('id', 'first_name', 'last_name', 'username', 'bio', 'avatar_id')
            ->with([
                'Avatar' => function($q) {
                    $q->select('id', 'main', 'thumbnail');
                },
                'Profile'
            ])
            ->where('username', $username)
            ->first();
    }

    public function updateUserDetail($userId, $data)
    {
        $profile = $data['profile'];
        unset($data['profile']);
        $this->userRepo->update($userId, $data);
        $userProfile = $this->userProfileRepo->getModelByField('user_id', $userId, true);
        $this->userProfileRepo->update($userProfile->id, $profile);

        return $this->getUserDetail($userId);
    }

    public function checkUsername(&$username)
    {
        $user = $this->userRepo->model->select('id', 'username')
            ->where('username', $username)
            ->orderBy('username', 'DESC')
            ->first();

        if (!$user) {
            return true;
        }

        $username = $user->username;
        preg_match('#(\d+)$#', $username, $matches);

        if (count($matches) == 0) {
            $username = $username . "1";
        } else {
            $digit = $matches[1];
            $lens = strlen($digit);
            $newUsername = substr($username, 0, -$lens);
            $digit = (int) $digit;
            $digit++;
            $username = $newUsername . "$digit";
        }

        $this->checkUsername($username);
    }

}
