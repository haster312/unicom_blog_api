<?php


namespace App\Services;


use App\Repositories\ArticleRepo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ArticleService extends BaseService
{
    public $articleRepo;

    public function __construct(ArticleRepo $articleRepo)
    {
        parent::__construct();
        $this->articleRepo = $articleRepo;
    }

    public function getPopularByPeriod($categoryId = null, $type = null)
    {
        $query = $this->articleRepo->model->with(
                [
                    'Author' => function($q) {
                        $q->select('id', 'first_name', 'last_name');
                    },
                    'Category'
                ]);

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

        return $query->orderBy('view_count', 'DESC')
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
        $articles = $this->articleRepo->model->with(
                [
                    'Author' => function($q) {
                        $q->select('id', 'first_name', 'last_name');
                    },
                    'Category'
                ])->where('category_id', $categoryId)->paginate($this->size);

        return $articles->toArray();
    }

    /**
     * Check if article belong to user
     * @param $userId
     * @param $id
     * @return mixed
     */
    public function checkArticle($userId, $id)
    {
        return $this->articleRepo->model->select('id')
                ->where('id', $id)
                ->where('author_id', $userId)
                ->first();
    }

    /**
     * Get detail by article Id
     * @param $id
     * @return mixed
     */
    public function getArticleById($id)
    {
        return $this->articleRepo->model->with(
                [
                    'Author' => function($q) {
                        $q->select('id', 'first_name', 'last_name');
                    },
                    'Category'
                ])->where('id', $id)->first();
    }

    /**
     * Get detail by article slug
     * @param $slug
     * @return mixed
     */
    public function getArticleBySlug($slug)
    {
        return $this->articleRepo->model->with(
            [
                'Author' => function($q) {
                    $q->select('id', 'username', 'avatar_id',
                        DB::raw('(select count(article.id) from article where article.author_id = users.id) as total_article'));
                },
                'Author.Avatar' => function($q) {
                    $q->select('id', 'thumbnail', 'main');
                },
                'Category'
            ])->where('slug', $slug)->first();
    }

    public function createArticle($data)
    {
        $data['slug'] = Str::slug($data['title']);
        $article = $this->articleRepo->create($data);

        return $this->getArticleById($article->id);
    }

    public function updateArticle($id, $data)
    {
        $data['slug'] = Str::slug($data['title']);
        $article = $this->articleRepo->update($id, $data);

        return $this->getArticleById($article->id);
    }
}
