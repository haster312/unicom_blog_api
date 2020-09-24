<?php


namespace App\Services;

use App\Helpers\CacheHelper;

class BaseService
{
    public $size = 10;
    public $page = 1;
    public $key;
    public $cacheHelper;

    public function __construct()
    {
        $this->getParams();
        $this->cacheHelper = new CacheHelper();
    }

    public function getParams()
    {
        $this->page = \request()->query('page') ? \request()->query('page') : 1;
        if (!is_numeric($this->page)) {
            error('Page must be a number');
        }
        $this->size = \request()->query('size') ? \request()->query('size') : 10;
        if (!is_numeric($this->size)) {
            error('Size must be a number');
        }

        $this->key = \request()->query('key') ? \request()->query('key') : null;
        $this->key = strip_tags($this->key);
    }
}
