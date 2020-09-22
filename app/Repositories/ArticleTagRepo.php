<?php

namespace App\Repositories;
use App\Repositories\BaseRepo;
use App\Models\ArticleTag;

class ArticleTagRepo extends BaseRepo
{
    public function getModel()
    {
        return ArticleTag::class;
    }

    public function modifyArticleTag($articleId, $tagIds = [])
    {
        if (count($tagIds) > 0) {
            $this->deleteByArticleId($articleId);
        }

        foreach ($tagIds as $tagId) {
            $this->create([
                'article_id' => $articleId,
                'tag_id' => $tagId
            ]);
        }

        return true;
    }

    public function deleteByArticleId($articleId)
    {
        return $this->deleteModelByField('article_id', $articleId);
    }
}
