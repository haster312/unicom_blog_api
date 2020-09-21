<?php

namespace App\Repositories;
use App\Repositories\BaseRepo;
use App\Models\Tag;

class TagRepo extends BaseRepo
{
    public function getModel()
    {
        return Tag::class;
    }

    public function checkExistOrCreate($tagName, $update = true)
    {
        $tagName = trim($tagName);
        $tag = $this->model->where('name', $tagName)->first();

        if (!$tag) {
            $tag = $this->create(['name' => $tagName]);
        } else {
            if (!$update) {
                $tag->count += 1;
                $tag->save();
            }
        }

        return $tag;
    }
}
