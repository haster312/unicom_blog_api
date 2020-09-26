<?php

namespace App\Observers;

use App\Models\Image;

class ImageObserver
{
    public function retrieved(Image $image)
    {
        $assetUrl = env('ASSET_URL');
        $main = $image->main;
        $thumb = $image->thumbnail;
        $mainURL = explode("uploads", $main);
        $thumbURL = explode("uploads", $thumb);

        $main = end($mainURL);
        $thumb = end($thumbURL);
        $image->main = $assetUrl . "/uploads" . $main;
        $image->thumbnail = $assetUrl . "/uploads" . $thumb;
    }
}
