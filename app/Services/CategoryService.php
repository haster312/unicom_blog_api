<?php


namespace App\Services;


use App\Models\SubCategory;
use App\Repositories\CategoryRepo;

class CategoryService
{
    public $categoryRepo;
    public $subCategoryRepo;

    public function __construct(CategoryRepo $categoryRepo, SubCategory $subCategoryRepo)
    {
        $this->categoryRepo = $categoryRepo;
        $this->subCategoryRepo = $subCategoryRepo;
    }

    public function getCategory()
    {
        return $this->categoryRepo->model->orderBy('order', 'ASC')->get();
    }
}
