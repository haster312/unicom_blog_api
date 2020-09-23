<?php


namespace App\Services;


use App\Repositories\ArticleCommentRepo;
use App\Repositories\ArticleLikeRepo;

class ArticleActionService extends BaseService
{
    public $articleLikeRepo;
    public $articleCommentRepo;

    public function __construct(ArticleLikeRepo $articleLikeRepo, ArticleCommentRepo $articleCommentRepo)
    {
        parent::__construct();
        $this->articleLikeRepo = $articleLikeRepo;
        $this->articleCommentRepo = $articleCommentRepo;
    }

    public function addLike($articleId, $userId)
    {
        $like = $this->articleLikeRepo->checkLike($articleId, $userId);

        if ($like) {
            if ($like->status == 0) {
                $like->status = 1;
            } else {
                $like->status = 0;
            }

            $like->save();
        } else {
            $like = $this->articleLikeRepo->create([
                'article_id' => $articleId,
                'user_id' => $userId,
                'status' => 1
            ]);
        }

        return $like;
    }
}
