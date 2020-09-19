<?php


namespace App\Services;

use App\Helpers\ImageHelper;
use App\Helpers\S3Helper;
use App\Jobs\RemoveImage;
use App\Repositories\ImageRepo;
use App\Http\Traits\CustomResponse;

class ImageService
{
    public $imageRepo;
    public $imageHelper;
    public $S3Helper;
    public $houseRepo;

    public function __construct(
        ImageRepo $imageRepo,
        ImageHelper $imageHelper,
        S3Helper $S3Helper
    ) {
        $this->imageRepo = $imageRepo;
        $this->imageHelper = $imageHelper;
        $this->S3Helper = $S3Helper;
    }

    /**
     * Upload image with mime
     * @param $file
     * @param $rotate
     * @return mixed|null
     */
    public function addImage($file)
    {
        $imageData = $this->imageHelper->getImage($file);
        $main = $imageData['main'];
        $thumbnail = $imageData['thumbnail'];

        if ($imageData) {
            $data['main'] = $this->S3Helper->uploadImage(public_path($imageData['main']), $imageData['main'], $imageData['mime']);
            $data['thumbnail'] = $this->S3Helper->uploadImage(
                public_path($imageData['thumbnail']),
                $imageData['thumbnail'],
                $imageData['mime']
            );
            $data['mime'] = $imageData['mime'];
            
            if (!$data['main'] && !$data['thumbnail']) {
                return null;
            }

            $image = $this->imageRepo->create($data);

            // Remove main
            RemoveImage::dispatch([
                'main' => public_path($main),
                'thumbnail' => public_path($thumbnail)
            ])->delay(now()->addSeconds(10));

            return $image;
        }

        return null;
    }
}
