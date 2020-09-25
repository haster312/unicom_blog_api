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
    private $tagIds = [];
    private $currentArticle;
    private $categoryRelation = [];
    private $countComment;
    private $countLike;
    private $isLike;

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
        ];

        $this->currentArticle = request('article_id') ? request('article_id') : null;
        $this->countComment = DB::raw('(select count(article_comment.id) from article_comment where article_id = article.id) as comment_count');
        $this->countLike = DB::raw('(select count(article_like.id) from article_like where article_id = article.id and article_like.status = 1) as like_count');
        $this->isLike = DB::raw('(select article_like.id from article_like
                                        where article_id = article.id and article_like.user_id = ? and article_like.status = 1)
                                        as is_like');
    }

    public function getLatestArticle()
    {
        $articles = $this->articleRepo->model
            ->select('id', 'title', 'short_content', 'cover_id', 'slug',
                'author_id', 'category_id', 'subcategory_id', 'view_count', 'status', 'created_at',
                $this->countLike
            )
            ->where('status', 1)
            ->with($this->categoryRelation)
            ->orderBy('created_at', 'DESC')
            ->paginate($this->size);

        return $articles->toArray();
    }

    public function getSelfArticle($userId)
    {
        $articles = $this->articleRepo->model
                    ->select('id', 'title', 'short_content', 'cover_id', 'slug',
                        'author_id', 'category_id', 'subcategory_id', 'view_count', 'status', 'created_at',
                        $this->countLike
                    )
                    ->with([
                        'Category' => function($q) {
                            $q->select('id', 'name', 'slug');
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
                    ])->where('author_id', $userId)->orderBy('created_at', 'DESC')->paginate($this->size);

        return $articles->toArray();
    }

    public function getPopularByPeriod($categoryId = null, $type = null)
    {
        $query = $this->articleRepo->model
            ->select('id', 'title', 'short_content', 'cover_id', 'slug',
                'author_id', 'category_id', 'subcategory_id', 'view_count', 'status', 'created_at',
                $this->countLike
            )
            ->with($this->categoryRelation);

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

        if ($this->currentArticle) {
            $query->where('id', '!=', $this->currentArticle);
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
            ->orderBy('created_at', 'DESC')
            ->paginate($this->size);

        return $articles->toArray();
    }

    public function getFeatureArticle()
    {
        return $this->articleRepo->model
            ->select('id', 'title', 'short_content', 'cover_id', 'slug',
                'author_id', 'category_id', 'subcategory_id', 'view_count', 'status', 'created_at',
                $this->countLike
            )
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
                        $q->select('id', 'name', 'slug');
                    },
                    'Author' => function($q) {
                        $q->select('id', 'first_name', 'last_name');
                    },
                    'ArticleTag' => function($q) {
                        $q->select('id', 'article_id', 'tag_id');
                    },
                    'ArticleTag.Tag' => function($q) {
                        $q->select('id', 'name', 'count');
                    },
                    'Cover' => function($q) {
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
     * @param $userId
     * @return mixed
     */
    public function getArticleBySlug($slug, $userId = null)
    {
        $query = $this->articleRepo->model->with(
            [
                'Category' => function($q) {
                    $q->select('id', 'name', 'slug');
                },
                'Author' => function($q) {
                    $q->select('id', 'username', 'avatar_id', 'bio',
                        DB::raw('(select count(article.id) from article where article.author_id = users.id) as total_article'));
                },
                'Author.Avatar' => function($q) {
                    $q->select('id', 'main', 'thumbnail');
                },
                'Cover' => function($q) {
                    $q->select('id', 'main', 'thumbnail');
                },
                'ArticleTag' => function($q) {
                    $q->select('article_tag.id', 'article_id', 'tag_id');
                    $q->leftJoin('tag', 'tag.id', 'article_tag.tag_id');
                    $q->orderBy('count', 'DESC');
                },
                'ArticleTag.Tag' => function($q) {
                    $q->select('id', 'name', 'count');
                },
            ]);


        if ($userId) {
            $query->select('*', $this->countComment, $this->countLike, $this->isLike);
            $query->where('slug', '?');
            $query->setBindings([$userId, $slug]);
        } else {
            $query->select('*', $this->countComment, $this->countLike);
            $query->where('slug', $slug);
        }

        $article = $query->first();

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
            $this->addArticleTag($tags, $article->id);
            $article = $this->getArticleById($article->id);

            DB::commit();
            return $article;
        } catch (\Exception $exception) {
           DB::rollBack();
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
            $this->addArticleTag($tags, $article->id);
            $article = $this->getArticleById($articleId);

            DB::commit();
            return $article;
        } catch (\Exception $exception) {
            DB::rollBack();
            return false;
        }
    }

    public function addArticleTag($tags, $articleId)
    {
        if (count($tags) > 0) {
            foreach ($tags as $tagName) {
                $tag = $this->tagRepo->checkExistOrCreate($tagName, false);
                $this->tagIds[] = $tag->id;
            }
        }

        if (count($this->tagIds) > 0) {
            $this->articleTagRepo->modifyArticleTag($articleId, $this->tagIds);
        }
    }

    public function publishArticle($articleId, $status)
    {
        $article = $this->articleRepo->getModelById($articleId);
        if (!$article) {
            return false;
        }

        $this->articleRepo->update($articleId, ['status' => $status]);
        return $this->getArticleById($articleId);
    }

    public function deleteArticle($articleId)
    {
        $deleted = $this->articleRepo->deleteModelByField('id', $articleId);
        if (!$deleted) {
            return false;
        }

        $this->articleTagRepo->deleteByArticleId($articleId);
        return true;
    }
}
