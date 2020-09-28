<?php

namespace App\Observers;

use App\Models\ArticleComment;
use App\Repositories\ArticleRepo;
use App\Repositories\NotificationRepo;

class CommentObserver
{
    private $articleRepo;
    private $notificationRepo;
    public function __construct(ArticleRepo $articleRepo, NotificationRepo $notificationRepo)
    {
        $this->articleRepo = $articleRepo;
        $this->notificationRepo = $notificationRepo;
    }

    public function created(ArticleComment $articleComment)
    {
        $article = $this->articleRepo->getModelById($articleComment->article_id);

        if ($articleComment->user_id != $article->author_id) {
            $data = [
                'user_id' => $articleComment->user_id,
                'target_id' => $article->author_id,
                'article_id' => $article->id,
                'comment_id' => $articleComment->id,
                'type' => constants('NOTI.COMMENT')
            ];

            $this->notificationRepo->create($data);
        }
    }
}
