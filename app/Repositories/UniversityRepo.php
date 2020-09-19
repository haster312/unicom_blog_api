<?php


namespace App\Repositories;

use App\Models\University;

class UniversityRepo extends BaseRepo
{
    public function getModel()
    {
        return University::class;
    }
}
