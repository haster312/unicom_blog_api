<?php

namespace App\Repositories;
use App\Repositories\BaseRepo;
use App\Models\Article;

class ArticleRepo extends BaseRepo
{
    public function getModel()
    {
        return Article::class;
    }
}
