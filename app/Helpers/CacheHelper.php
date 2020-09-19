<?php
namespace App\Helpers;


use Illuminate\Support\Facades\Redis;

class CacheHelper
{
    public $items;
    public $redis;

    public function __construct()
    {
        $this->items = [];
        $this->redis = Redis::connection();
    }

    /**
     * Set cache for pagination list by type
     * @param $type
     * @param $data
     * @return mixed
     */
    public function setCache($type, $data)
    {
        $cached = json_encode($data);
        return $this->redis->set($type, $cached);
    }

    public function getCache($type)
    {
        $cacheData = $this->redis->get($type);
        $cacheData = json_decode($cacheData);

        return $cacheData ?? [];
    }

    public function getPaginationCache($type, $page, $size)
    {
        $cacheData = $this->redis->get($type);
        $min = ($page - 1) * $size;
        $max = $page * $size;
        $cacheData = json_decode($cacheData);

        if ($cacheData && count($cacheData) > 0) {
            foreach ($cacheData as $index => $data) {
                if ($page == 1) {
                    if ($index < $max) {
                        $this->items[] = $data;
                    }
                } else {
                    if ($index >= $min && $index < $max) {
                        $this->items[] = $data;
                    }
                }
            }

            return $this->items;
        }

        return $this->items;
    }
}
