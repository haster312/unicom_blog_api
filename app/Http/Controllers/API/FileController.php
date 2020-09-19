<?php

namespace App\Http\Controllers\API;

use App\Services\ImageService;
use Illuminate\Http\Request;

class FileController
{
    private $imageService;
    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function single(Request $request)
    {
        $file = $request->file('file');
        if (!$file) {
            error(messages('MissingFile'));
        }

        try {
            $image = $this->imageService->addImage($file);
            if (!$image) {
                error(messages('Error'));
            }

            success($image);
        } catch (\Exception $exception) {
            error($exception->getMessage());
        }

    }
}
