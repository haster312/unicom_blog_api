<?php

namespace App\Observers;

use App\Helpers\Firebase;
use App\Models\ArticleComment;
use App\Repositories\ArticleRepo;
use App\Repositories\NotificationRepo;
use App\Repositories\NotificationTokenRepo;

class CommentObserver
{
    private $articleRepo;
    private $notificationRepo;
    private $firebase;
    private $notificationTokenRepo;

    public function __construct(
        ArticleRepo $articleRepo,
        NotificationRepo $notificationRepo,
        NotificationTokenRepo $notificationTokenRepo,
        Firebase $firebase
    )
    {
        $this->articleRepo = $articleRepo;
        $this->notificationRepo = $notificationRepo;
        $this->firebase = $firebase;
        $this->notificationTokenRepo = $notificationTokenRepo;
    }

    public function created(ArticleComment $articleComment)
    {
        $article = $this->articleRepo->getModelById($articleComment->article_id);

        if ($articleComment->user_id != $article->author_id) {
            $targetId = $article->author_id;

            $data = [
                'user_id' => $articleComment->user_id,
                'target_id' => $targetId,
                'article_id' => $article->id,
                'comment_id' => $articleComment->id,
                'type' => constants('NOTI.COMMENT')
            ];

            $this->notificationRepo->create($data);

            $notiToken = $this->notificationTokenRepo->getModelByFields([
                ['user_id', $targetId],
                ['type', 'Web']
            ], true);

            if ($notiToken) {
                $this->firebase->sendMessage($notiToken->token, [
                    'title' => 'New comment',
                    'body' => 'Your article has new comment',
                    'meta' => [
                        'article_id' => $article->id
                    ]
                ]);
            }
        }
    }
}
