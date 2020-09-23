<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
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
}
