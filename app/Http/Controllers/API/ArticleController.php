<?php

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleRequest;
use App\Services\ArticleService;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    private $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    public function getSelfArticle()
    {
        $this->getUser($user);
        $articles = $this->articleService->getSelfArticle($user->id);

        paging($articles);
    }

    /**
     * Get feature article for home page
     */
    public function getFeature()
    {
        try {
            $articles = $this->articleService->getFeatureArticle();

            success($articles);
        } catch (\Exception $exception) {
            error($exception->getMessage());
        }
    }

    public function getLatest()
    {
        try {
            $articles = $this->articleService->getLatestArticle();

            paging($articles);
        } catch (\Exception $exception) {
            error($exception->getMessage());
        }
    }

    /**
     * Get most popular for home page
     */
    public function getMostPopular()
    {
        try {
            $articles = $this->articleService->getPopularByPeriod();

            success($articles);
        } catch (\Exception $exception) {
            error($exception->getMessage());
        }
    }

    /**
     * Weekly popular relate to current article's category
     * @param Request $request
     */
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

    /**
     * monthly popular relate to current article's category
     * @param Request $request
     */
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

    public function detail($id)
    {
        $this->getUser($user);
        $article = $this->articleService->checkArticle($id, $user->id);

        if (!$article) {
            error(messages('Forbidden'));
        }

        $article = $this->articleService->getArticleById($id, $user->id);
        if (!$article) {
            error(messages('NotExist'), 404);
        }

        success($article);
    }

    public function detailSlug(Request $request, $slug)
    {
        $data = getData($request);
        $userId = isset($data['user_id']) ? $data['user_id'] : null;

        $article = $this->articleService->getArticleBySlug($slug, $userId);
        if (!$article) {
            error(messages('NotExist'), 404);
        }

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
        $article = $this->articleService->checkArticle($id, $user->id);

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

    public function publishArticle(Request $request, $id)
    {
        $data = getData($request);
        try {
            $article = $this->articleService->publishArticle($id, $data['status']);

            success($article);
        } catch (\Exception $exception) {
            error($exception->getMessage());
        }
    }

    public function delete($articleId)
    {
        $article = $this->articleService->articleRepo->getModelById($articleId);
        if (!$article) {
            success(true);
        }

        $deleted = $this->articleService->deleteArticle($articleId);
        if (!$deleted) {
            error(messages('Error'));
        }

        success($deleted);
    }
}
