<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Image extends Model
{
    use Base;
    protected $table = 'image';
    protected $fillable = ['main', 'thumbnail', 'mime', 'type'];

    public function getMainAttribute($value)
    {
        if (str_contains($value, "s3")) {
            $assetUrl = env('ASSET_URL', "https://asset.myunicoms.com");
            $value = explode("uploads", $value);
            $url = end($value);

            return $assetUrl . "/uploads" . $url;
        }

        return $value;
    }

    public function getThumbnailAttribute($value)
    {
        if (str_contains($value, "s3")) {
            $assetUrl = env('ASSET_URL', "https://asset.myunicoms.com");
            $value = explode("uploads", $value);
            $url = end($value);

            return $assetUrl . "/uploads" . $url;
        }

        return $value;
    }
}
