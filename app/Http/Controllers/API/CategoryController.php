<?php
namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    private $categoryService;
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function list()
    {
        $categories = $this->categoryService->getCategory();

        success($categories);
    }

    public function detail($slug)
    {
        if (!$slug) {
            success([]);
        }

        $category = $this->categoryService->categoryRepo->getModelByField('slug', $slug, true);
        success($category);
    }
}
