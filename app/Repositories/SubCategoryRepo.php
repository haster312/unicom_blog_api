<?php

namespace App\Repositories;
use App\Repositories\BaseRepo;
use App\Models\SubCategory;

class SubCategoryRepo extends BaseRepo
{
    public function getModel()
    {
        return SubCategory::class;
    }

}
