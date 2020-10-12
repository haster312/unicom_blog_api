<?php

namespace App\Models;

use App\Models\Base;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use Base;
    protected $table = 'notification';
    protected $fillable = ['user_id', 'target_id', 'article_id', 'type', 'seen', 'comment_id', 'request_id'];

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function Article()
    {
        return $this->belongsTo(Article::class, 'article_id', 'id');
    }
}
