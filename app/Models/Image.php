<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use Base;
    protected $table = 'image';
    protected $fillable = ['main', 'thumbnail', 'mime', 'type'];

    public function getMainAttribute($value)
    {
        $assetUrl = env('ASSET_URL');
        $value = explode("uploads", $value);
        $url = end($value);

        return $assetUrl . "/uploads" . $url;
    }

    public function getThumbnailAttribute($value)
    {
        $assetUrl = env('ASSET_URL');
        $value = explode("uploads", $value);
        $url = end($value);

        return $assetUrl . "/uploads" . $url;
    }
}
