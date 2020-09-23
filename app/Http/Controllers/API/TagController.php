<?php


namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Services\TagService;

class TagController extends Controller
{
    public $tagService;
    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    public function getFooterTag()
    {
        $tags = $this->tagService->getTagCloud();
        success($tags);
    }
}
