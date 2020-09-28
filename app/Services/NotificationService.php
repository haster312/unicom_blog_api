<?php


namespace App\Services;


use App\Repositories\NotificationRepo;

class NotificationService extends BaseService
{
    public $notificationRepo;
    public function __construct(NotificationRepo $notificationRepo)
    {
        parent::__construct();
        $this->notificationRepo = $notificationRepo;
    }

    public function addNotification($data)
    {
       return $this->notificationRepo->create($data);
    }

    public function getUserNotification($userId)
    {
        $notifications = $this->notificationRepo->model
            ->with([
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
            ->paginate($this->size);

        return $notifications->toArray();
    }

    public function setNotiSeen($userId, $notiId)
    {
        $noti = $this->notificationRepo->getModelByFields([
            ['target_id', $userId],
            ['id', $notiId]
        ], true);

        if (!$noti) {
            return false;
        }

        $noti = $this->notificationRepo->update($notiId, ['seen' => 1]);

        return $noti;
    }
}
