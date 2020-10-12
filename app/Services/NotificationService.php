<?php


namespace App\Services;


use App\Repositories\NotificationRepo;
use App\Repositories\NotificationTokenRepo;
use Illuminate\Support\Facades\DB;

class NotificationService extends BaseService
{
    public $notificationRepo;
    public $notificationTokenRepo;

    public function __construct(NotificationRepo $notificationRepo, NotificationTokenRepo $notificationTokenRepo)
    {
        parent::__construct();
        $this->notificationRepo = $notificationRepo;
        $this->notificationTokenRepo = $notificationTokenRepo;
    }

    public function addNotification($data)
    {
       return $this->notificationRepo->create($data);
    }

    public function getLatestNotification($userId)
    {
        return $this->notificationRepo->model->with([
                'User' => function($q) {
                    $q->select('id', 'first_name', 'last_name', 'avatar_id');
                },
                'User.Avatar' => function($q) {
                    $q->select('id', 'main', 'thumbnail');
                },
                'Article' => function($q) {
                    $q->select('id', 'title', 'slug');
                }
            ])
            ->where('target_id', $userId)
            ->where('seen', 0)
            ->orderBy('created_at', 'DESC')
            ->first();
    }

    public function getChatNotification($userId)
    {
        return $this->notificationRepo->model
            ->with([
                'User' => function($q) {
                    $q->select('id', 'first_name', 'last_name', 'avatar_id', DB::raw("CONCAT( last_name, ' ', first_name ) AS name"));
                },
                'User.Avatar' => function($q) {
                    $q->select('id', 'main', 'thumbnail');
                }
            ])
            ->where('target_id', $userId)
            ->where('seen', 0)
            ->where(function ($q) {
                $q->where('type', constants('NOTI.FRIEND_REQUEST'));
                $q->orWhere('type', constants('NOTI.ACCEPT_REQUEST'));
                $q->orWhere('type', constants('NOTI.REJECT_REQUEST'));
            })
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    public function getUserNotification($userId)
    {
        return $this->notificationRepo->model
            ->with([
                'User' => function($q) {
                    $q->select('id', 'first_name', 'last_name', 'avatar_id', DB::raw("CONCAT( last_name, ' ', first_name ) AS name"));
                },
                'User.Avatar' => function($q) {
                    $q->select('id', 'main', 'thumbnail');
                },
                'Article' => function($q) {
                    $q->select('id', 'title', 'slug');
                }
            ])
            ->where('target_id', $userId)
            ->where('seen', 0)
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    public function setNotiSeen($userId, $notiId)
    {
        $noti = $this->notificationRepo->getModelByFields([
            ['target_id', $userId],
            ['id', $notiId]
        ], true);

        if (!$noti) {
            $this->notificationRepo->update($notiId, ['seen' => 1]);
        }

        $noti = $this->notificationRepo->update($notiId, ['seen' => 1]);

        return $noti;
    }

    public function addUserNotificationToken($userId, $token)
    {
        $notiToken = $this->notificationTokenRepo->getModelByFields([
            ['user_id' => $userId],
            ['type' => 'Web']
        ], true);

        if ($notiToken) {
            return $this->notificationTokenRepo->update($notiToken->id, ['token' => $token]);
        }

        return $this->notificationTokenRepo->create([
            'user_id' => $userId,
            'type' => 'Web',
            'token' => $token
        ]);
    }

}
