<?php


namespace App\Services;


use App\Repositories\ArticleRepo;
use App\Repositories\TagRepo;
use Illuminate\Support\Facades\DB;

class SearchService extends BaseService
{
    public $articleRepo;
    private $searchable = [];
    private $categoryId;
    private $tags;
    private $tagRepo;
    public function __construct(ArticleRepo $articleRepo, TagRepo $tagRepo)
    {
        parent::__construct();
        $this->articleRepo = $articleRepo;
        $this->tagRepo = $tagRepo;
        $this->searchable = $this->articleRepo->model->searchable;
        $this->searchable = implode(',', $this->searchable);
        $this->categoryId = \request()->query('category') ? \request()->query('category') : null;
        $this->tags = \request()->query('tags') ? \request()->query('tags') : [];
    }

    public function searchArticleByOption()
    {
        $query = $this->articleRepo->model->with([
                    'Category' => function($q) {
                        $q->select('id', 'name', 'slug');
                    },
                    'Author' => function($q) {
                        $q->select('id', 'first_name', 'last_name', 'username');
                    },
                    'Author.Avatar' => function($q) {
                        $q->select('id', 'main', 'thumbnail');
                    },
                    'Cover' => function($q) {
                        $q->select('id', 'main', 'thumbnail');
                    },
                    'ArticleTag' => function($q) {
                        $q->select('id', 'article_id', 'tag_id');
                    },
                    'ArticleTag.Tag' => function($q) {
                        $q->select('id', 'name', 'count');
                    }
            ])->select('article.id', 'title', 'author_id', 'slug', 'category_id', 'status', 'short_content', 'content', 'slug', 'thumbnail_id'
                        ,'view_count', 'article.created_at');
        // Only published
        $query->where('status', 1);

        if (count($this->tags) > 0) {
            $tagId = $this->tagRepo->getIdByTagName($this->tags);
            $query->leftJoin('article_tag', 'article_tag.article_id', 'article.id');
            $query->whereIn('article_tag.tag_id', $tagId);
        }

        if ($this->key) {
            $query->where(function ($q) {
                $q->whereRaw("MATCH ({$this->searchable}) AGAINST ('$this->key' IN BOOLEAN MODE)");
                $q->orWhere('title', 'LIKE', "%$this->key%");
            });
        }

        if ($this->categoryId) {
            $query->where('article.category_id', $this->categoryId);
        }

        if (count($this->tags) > 0) {
            $query->distinct('article.id');
        }

        $articles = $query->orderBy('view_count', 'DESC')->paginate($this->size);

        return $articles->toArray();
    }

    protected function fullTextWildcards($term)
    {
        // removing symbols used by MySQL
        $reservedSymbols = ['-', '+', '<', '>', '@', '(', ')', '~'];
        $term = str_replace($reservedSymbols, '', $term);

        $words = explode(' ', $term);

        foreach ($words as $key => $word) {
            /*
             * applying + operator (required word) only big words
             * because smaller ones are not indexed by mysql
             */
            if (strlen($word) >= 3) {
                $words[$key] = '*' . $word . '*';
            }
        }

        return implode(' ', $words);
    }
}
