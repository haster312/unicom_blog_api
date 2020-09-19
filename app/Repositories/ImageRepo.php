<?php


namespace App\Repositories;


use App\Models\Image;

class ImageRepo extends BaseRepo
{
    public function getModel()
    {
        return Image::class;
    }
}
