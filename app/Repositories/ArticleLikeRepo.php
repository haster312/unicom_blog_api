<?php

namespace App\Repositories;
use App\Repositories\BaseRepo;
use App\Models\ArticleLike;

class ArticleLikeRepo extends BaseRepo
{
    public function getModel()
    {
        return ArticleLike::class;
    }

    public function checkLike($articleId, $userId)
    {
        return $this->model->where('article_id', $articleId)->where('user_id', $userId)->first();
    }
}
