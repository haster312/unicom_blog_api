<?php


namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Services\SearchService;

class SearchController extends Controller
{
    private $searchService;
    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function searchArticle()
    {
        $articles = $this->searchService->searchArticleByOption();
        success($articles['data']);
    }

    public function advancedSearchArticle()
    {
        $articles = $this->searchService->searchArticleByOption();
        paging($articles);
    }

}
