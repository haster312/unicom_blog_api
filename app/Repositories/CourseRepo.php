<?php

namespace App\Repositories;

use App\Models\Course;

class CourseRepo extends BaseRepo
{
    public function getModel()
    {

        return Course::class;
    }
}
