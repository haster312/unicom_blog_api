<?php

namespace App\Helpers;


use Intervention\Image\Image;
use Intervention\Image\ImageManager;

class ImageHelper
{
    protected $uploadFolder = "uploads/media/";
    public $originalDestination;
    protected $thumbDestination;
    public $manager;
    private $maxSize;
    protected $allowMime = [
        'gif', 'png', 'jpg', 'jpeg'
    ];

    public function __construct()
    {
        $this->originalDestination = $this->uploadFolder . date('Ym');
        $this->thumbDestination = $this->originalDestination . "/thumb";
        $this->maxSize = config('const.Image.MaxSize');
    }

    public function getDataBase64($path)
    {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        return $base64;
    }

    public function getImage($file)
    {
        $mimeType = $file->getMimeType();
        $original = $this->uploadImage($file);

        if ($original) {
            //Resize large image
            $fileName = $this->originalDestination . '/' . $original;
            $thumbnail = $this->generateThumbnail($original);

            return [
                'main' => $fileName,
                'thumbnail' => $thumbnail,
                'mime' => $mimeType
            ];
        }

        return null;
    }

    /**
     * Get dimension of main, thumbnail image
     * @param $photo
     * @return array
     */
    public function getDimension($photo)
    {
        list($mainWidth, $mainHeight) = getimagesize($photo['main']);
        list($thumbWidth, $thumbHeight) = getimagesize($photo['thumbnail']);

        return [
            'main_width' => $mainWidth,
            'main_height' => $mainHeight,
            'thumbnail_width' => $thumbWidth,
            'thumbnail_height' => $thumbHeight,
        ];
    }

    /**
     * Upload original image
     * @param $file
     * @return bool|array
     */
    public function uploadImage($file)
    {
        $oldMask = umask();
        umask(0);
        if (!is_dir('./' . $this->originalDestination)) {
            mkdir('./' . $this->originalDestination, 0777, true);
        }

        if (!is_dir('./' . $this->thumbDestination)) {
            mkdir('./' . $this->thumbDestination, 0777, true);
        }
        umask($oldMask);

        $extension = $file->getClientOriginalExtension();

        if (!in_array($extension, $this->allowMime)) {
            return false;
        }

        $fileName = rand(111, 999999999) . '.' . $extension;

        while (file_exists("./" . $this->originalDestination . '/' . $fileName)) {
            $fileName = rand(111, 999999999) . '.' . $extension;
        }

        $file->move($this->originalDestination, $fileName);

        return $fileName;
    }

    /**
     * Generate thumbnail from file path
     * @param $fileName
     * @return bool|string
     */
    public function generateThumbnail($fileName)
    {
        // Set a maximum height and width
        $width = 500;
        $height = 500;

        // open an image file
        if (!$fileName) {
            return false;
        }

        list($originalWidth, $originalHeight) = getimagesize("./" . $this->originalDestination . "/" . $fileName);

        $ratio = $originalWidth / $originalHeight;

        if ($width / $height > $ratio) {
            $width = $height * $ratio;
        } else {
            $height = $width / $ratio;
        }

        //generate new thumbnail
        $image = \Image::make($this->originalDestination . "/" . $fileName);

        $thumbName = $this->thumbDestination . "/thumb_" . $fileName;

        // now you are able to resize the instance
        $image->resize($width, $height);

        $image->save($thumbName, 100);

        return $thumbName;
    }
}
