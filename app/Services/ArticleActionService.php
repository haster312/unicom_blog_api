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
                $status = 1;
            } else {
                $status = 0;
            }

            $like = $this->articleLikeRepo->update($like->id, ['status' => $status]);
        } else {
            $like = $this->articleLikeRepo->create([
                'article_id' => $articleId,
                'user_id' => $userId,
                'status' => 1
            ]);
        }

        return $like;
    }

    public function getCommentDetail($commentId)
    {
        return $this->articleCommentRepo->model
                ->with([
                    'Author' => function($q) {
                        $q->select('id', 'first_name', 'last_name', 'username', 'avatar_id');
                    },
                    'Author.Avatar' => function($q) {
                        $q->select('id', 'main', 'thumbnail');
                    }
                ])
                ->where('id', $commentId)->first();
    }

    public function getArticleComment($articleId)
    {
        $comments = $this->articleCommentRepo->model
            ->with([
                'Author' => function($q) {
                    $q->select('id', 'first_name', 'last_name', 'username', 'avatar_id');
                },
                'Author.Avatar' => function($q) {
                    $q->select('id', 'main', 'thumbnail');
                },
                'Reply',
                'Reply.Author' => function($q) {
                    $q->select('id', 'first_name', 'last_name', 'username', 'avatar_id');
                },
                'Reply.Author.Avatar' => function($q) {
                    $q->select('id', 'main', 'thumbnail');
                },
            ])
            ->where('article_id', $articleId)
            ->whereNull('parent')
            ->orderBy('created_at', 'ASC')
            ->paginate($this->size);

        return $comments->toArray();
    }

    public function addComment($data)
    {
        $comment =  $this->articleCommentRepo->create($data);

        return $this->getCommentDetail($comment->id);
    }
}
