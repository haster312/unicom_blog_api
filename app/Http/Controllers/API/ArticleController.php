<?php

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleRequest;
use App\Services\ArticleService;
use Illuminate\Http\Request;
use mysql_xdevapi\Exception;

class ArticleController extends Controller
{
    private $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    public function getFeatured()
    {

    }

    public function getLatest()
    {

    }

    public function getMostPopular()
    {
        try {
            $articles = $this->articleService->getPopularByPeriod();

            success($articles);
        } catch (\Exception $exception) {
            error($exception->getMessage());
        }
    }

    public function getWeeklyPopular(Request $request)
    {
        $data = getData($request);
        $type = 'week';

        try {
            $articles = $this->articleService->getPopularByPeriod($data['category_id'], $type);

            success($articles);
        } catch (\Exception $exception) {
            error($exception->getMessage());
        }
    }

    public function getMonthlyPopular(Request $request)
    {
        $data = getData($request);
        $type = 'month';

        try {
            $articles = $this->articleService->getPopularByPeriod($data['category_id'], $type);

            success($articles);
        } catch (\Exception $exception) {
            error($exception->getMessage());
        }
    }

    public function getListWithCategory($categoryId)
    {
        $articles = $this->articleService->getArticlesByCategory($categoryId);
        paging($articles);
    }

    public function detail($slug)
    {
        $article = $this->articleService->getArticleBySlug($slug);
        // Return detail
        success($article);
    }

    public function new(ArticleRequest $request)
    {
        $this->getUser($user);
        $data = getData($request);
        $data['author_id'] = $user->id;

        try {
            $article = $this->articleService->createArticle($data);

            success($article);
        } catch (\Exception $exception) {
            error($exception->getMessage());
        }
    }

    public function update(ArticleRequest $request, $id)
    {
        $this->getUser($user);
        $data = getData($request);
        $article = $this->articleService->checkArticle($user->id, $data);

        if (!$article) {
            error(messages('Forbidden'));
        }

        try {
            $article = $this->articleService->updateArticle($id, $data);

            success($article);
        } catch (\Exception $exception) {
            error($exception->getMessage());
        }
    }

    public function delete()
    {

    }
}
