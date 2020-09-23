<?php

namespace App\Models;

use App\Models\Base;
use Illuminate\Database\Eloquent\Model;

class ArticleComment extends Model
{
    use Base;
    protected $table = 'article_comment';
    protected $fillable = ['article_id', 'user_id', 'content'];
}
