<?php


namespace App\Services;


use App\Repositories\ArticleTagRepo;
use App\Repositories\TagRepo;

class TagService extends BaseService
{
    public $tagRepo;
    public $articleTagRepo;

    public function __construct(TagRepo $tagRepo, ArticleTagRepo $articleTagRepo)
    {
        parent::__construct();
        $this->tagRepo = $tagRepo;
        $this->articleTagRepo = $articleTagRepo;
    }

    public function getTagCloud()
    {
        return $this->tagRepo->model->select('id', 'name', 'count')->orderBy('count', 'DESC')->limit($this->size)->get();
    }
}
