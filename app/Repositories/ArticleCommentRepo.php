<?php

namespace App\Repositories;
use App\Repositories\BaseRepo;
use App\Models\ArticleComment;

class ArticleCommentRepo extends BaseRepo
{
    public function getModel()
    {
        return ArticleComment::class;
    }

}
