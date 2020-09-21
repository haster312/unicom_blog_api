<?php


namespace App\Services;


use App\Repositories\ArticleRepo;
use App\Repositories\ArticleTagRepo;
use App\Repositories\TagRepo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ArticleService extends BaseService
{
    public $articleRepo;
    public $tagRepo;
    public $articleTagRepo;
    public $tagIds = [];
    public $singleRelation = [];
    public $categoryRelation = [];

    public function __construct(
        ArticleRepo $articleRepo,
        TagRepo $tagRepo,
        ArticleTagRepo $articleTagRepo)
    {
        parent::__construct();
        $this->articleRepo = $articleRepo;
        $this->tagRepo = $tagRepo;
        $this->articleTagRepo = $articleTagRepo;
        $this->categoryRelation = [
            'Category' => function($q) {
                $q->select('id', 'name');
            },
            'Author' => function($q) {
                $q->select('id', 'first_name', 'last_name');
            },
            'Author.Avatar' => function($q) {
                $q->select('id', 'main', 'thumbnail');
            },
            'Thumbnail' => function($q) {
                $q->select('id', 'main', 'thumbnail');
            },
            'ArticleTag' => function($q) {
                $q->select('id', 'article_id', 'tag_id');
            },
            'ArticleTag.Tag' => function($q) {
                $q->select('id', 'name');
            }
        ];
    }

    public function getSelfArticle($userId)
    {
        $articles = $this->articleRepo->model->with([
                    'Category' => function($q) {
                        $q->select('id', 'name');
                    },
                    'Thumbnail' => function($q) {
                        $q->select('id', 'main', 'thumbnail');
                    },
                    'ArticleTag' => function($q) {
                        $q->select('id', 'article_id', 'tag_id');
                    },
                    'ArticleTag.Tag' => function($q) {
                        $q->select('id', 'name');
                    }
                ])->where('author_id', $userId)->orderBy('created_at', 'DESC')->paginate($this->size);

        return $articles->toArray();
    }

    public function getPopularByPeriod($categoryId = null, $type = null)
    {
        $query = $this->articleRepo->model->with($this->categoryRelation);

        switch ($type) {
            case 'week':
                $startTime = Carbon::now()->startOfWeek()->timestamp;
                $endTime = Carbon::now()->endOfWeek()->timestamp;
                break;
            case 'month':
                $startTime = Carbon::now()->startOfMonth()->timestamp;
                $endTime = Carbon::now()->endOfMonth()->timestamp;
                break;
            default:
                $startTime = null;
                $endTime = null;
                break;
        }

        if ($startTime && $endTime) {
            $query->whereBetween('created_at', [$startTime, $endTime]);
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        return $query->where('status', 1)
                ->orderBy('view_count', 'DESC')
                ->limit($this->size)
                ->get();
    }

    /**
     * Get article by category Id
     * @param $categoryId
     * @return mixed
     */
    public function getArticlesByCategory($categoryId)
    {
        $articles = $this->articleRepo->model->with($this->categoryRelation)
            ->where('category_id', $categoryId)
            ->where('status', 1)
            ->paginate($this->size);

        return $articles->toArray();
    }

    public function getFeatureArticle()
    {
        return $this->articleRepo->model
            ->with($this->categoryRelation)
            ->where('status', 1)
            ->orderBy('view_count', 'DESC')
            ->limit($this->size)->get();
    }

    /**
     * Check if article belong to user
     * @param $userId
     * @param $articleId
     * @return mixed
     */
    public function checkArticle($articleId, $userId)
    {
        return $this->articleRepo->model->select('id')
                ->where('id', $articleId)
                ->where('author_id', $userId)
                ->first();
    }

    /**
     * Get detail by article Id or with userId
     * @param $id
     * @param $userId
     * @return mixed
     */
    public function getArticleById($id, $userId = null)
    {
        $query = $this->articleRepo->model->with(
                [
                    'Category' => function($q) {
                        $q->select('id', 'name');
                    },
                    'Author' => function($q) {
                        $q->select('id', 'first_name', 'last_name');
                    },
                    'ArticleTag' => function($q) {
                        $q->select('id', 'article_id', 'tag_id');
                    },
                    'ArticleTag.Tag' => function($q) {
                        $q->select('id', 'name');
                    },
                    'Thumbnail' => function($q) {
                        $q->select('id', 'main', 'thumbnail');
                    },
                ]);

        if ($userId) {
            $query->where('author_id', $userId);
        }

        return $query->where('id', $id)->first();
    }

    /**
     * Get detail by article slug for article detail + increase view count = 1
     * @param $slug
     * @return mixed
     */
    public function getArticleBySlug($slug)
    {
        $article = $this->articleRepo->model->with(
            [
                'Category' => function($q) {
                    $q->select('id', 'name');
                },
                'Author' => function($q) {
                    $q->select('id', 'username', 'avatar_id',
                        DB::raw('(select count(article.id) from article where article.author_id = users.id) as total_article'));
                },
                'Author.Avatar' => function($q) {
                    $q->select('id', 'main', 'thumbnail');
                },
                'Thumbnail' => function($q) {
                    $q->select('id', 'main', 'thumbnail');
                },
                'ArticleTag' => function($q) {
                    $q->select('id', 'article_id', 'tag_id');
                },
                'ArticleTag.Tag' => function($q) {
                    $q->select('id', 'name');
                },
            ])->where('slug', $slug)->first();

        if ($article) {
            $article->view_count +=1;
            $article->save();
        }

        return $article;
    }

    public function createArticle($data)
    {
        try {
            DB::beginTransaction();
            $data['slug'] = Str::slug($data['title']);
            $tags = [];
            if (isset($data['tags'])) {
                $tags = $data['tags'];
                unset($data['tags']);
            }

            $article = $this->articleRepo->create($data);

            if (count($tags) > 0) {
                foreach ($tags as $tagName) {
                    $tag = $this->tagRepo->checkExistOrCreate($tagName, false);
                    $this->tagIds[] = $tag->id;
                }
            }

            if (count($this->tagIds) > 0) {
                $this->articleTagRepo->modifyArticleTag($article->id, $this->tagIds);
            }

            return $article;
        } catch (\Exception $exception) {
           DB::rollBack();
           dd($exception->getMessage());
           return false;
        }
    }

    public function updateArticle($articleId, $data)
    {
        try {
            DB::beginTransaction();
            $data['slug'] = Str::slug($data['title']);
            $tags = [];
            if (isset($data['tags'])) {
                $tags = $data['tags'];
                unset($data['tags']);
            }

            $article = $this->articleRepo->update($articleId, $data);
            if (count($tags) > 0) {
                foreach ($tags as $tagName) {
                    $tag = $this->tagRepo->checkExistOrCreate($tagName, false);
                    $this->tagIds[] = $tag->id;
                }
            }

            if (count($this->tagIds) > 0) {
                $this->articleTagRepo->modifyArticleTag($article->id, $this->tagIds);
            }

            DB::commit();
            return $article;
        } catch (\Exception $exception) {
            DB::rollBack();
            return false;
        }
    }
}
