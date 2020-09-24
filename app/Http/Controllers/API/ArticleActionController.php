<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleCommentRequest;
use App\Services\ArticleActionService;
use Illuminate\Http\Request;

class ArticleActionController extends Controller
{
    private $articleActionService;

    public function __construct(ArticleActionService $articleActionService)
    {
        $this->articleActionService = $articleActionService;
    }

    public function likeArticle(Request $request)
    {
        $this->getUser($user);
        $data = getData($request);
        $like = $this->articleActionService->addLike($data['article_id'], $user->id);

        success($like);
    }

    public function getComment(Request $request)
    {
        $data = getData($request);
        if (!isset($data['article_id'])) {
            success([]);
        }

        $comments = $this->articleActionService->getArticleComment($data['article_id']);

        paging($comments);
    }

    public function commentArticle(ArticleCommentRequest $request)
    {
        $this->getUser($user);
        $data = getData($request);
        $data['user_id'] = $user->id;

        try {
            $comment = $this->articleActionService->addComment($data);

            success($comment);
        } catch (\Exception $exception) {
            error($exception->getMessage());
        }
    }
}
