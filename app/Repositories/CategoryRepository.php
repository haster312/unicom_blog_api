<?php

namespace App\Repositories;
use App\Repositories\BaseRepo;
use App\Models\Category;

class CategoryRepo extends BaseRepo
{
    public function getModel()
    {
        return Category::class;
    }

}
