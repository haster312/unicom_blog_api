<?php

namespace App\Models;

use App\Models\Base;
use Illuminate\Database\Eloquent\Model;

class ArticleLike extends Model
{
    use Base;
    protected $table = 'article_like';
    protected $fillable = ['article_id', 'user_id', 'status'];
}
