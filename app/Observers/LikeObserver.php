<?php

namespace App\Observers;

use App\Models\ArticleLike;
use App\Repositories\ArticleRepo;
use App\Repositories\NotificationRepo;

class LikeObserver
{
    private $articleRepo;
    private $notificationRepo;
    public function __construct(ArticleRepo $articleRepo, NotificationRepo $notificationRepo)
    {
        $this->articleRepo = $articleRepo;
        $this->notificationRepo = $notificationRepo;
    }

    public function created(ArticleLike $articleLike)
    {
        $article = $this->articleRepo->getModelById($articleLike->article_id);

        if ($articleLike->user_id != $article->author_id) {
            $data = [
                'user_id' => $articleLike->user_id,
                'target_id' => $article->author_id,
                'article_id' => $article->id,
                'type' => constants('NOTI.LIKE')
            ];

            $this->notificationRepo->create($data);
        }
    }
}
