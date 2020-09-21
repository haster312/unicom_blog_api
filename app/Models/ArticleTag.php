<?php

namespace App\Models;

use App\Models\Base;
use Illuminate\Database\Eloquent\Model;

class ArticleTag extends Model
{
    use Base;
    protected $table = 'article_tag';
    protected $fillable = ['article_id', 'tag_id'];

    public function Tag()
    {
        return $this->belongsTo(Tag::class , 'tag_id', 'id');
    }
}
