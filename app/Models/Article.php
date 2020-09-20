<?php

namespace App\Models;

use App\Models\Base;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use Base;
    protected $table = 'article';
    protected $fillable = ['title', 'slug', 'content', 'author_id', 'category_id', 'subcategory_id', 'status'];

    public function Author()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    public function Category()
    {
        return $this->belongsTo(Category::class ,'category_id', 'id');
    }

    public function setContentAttribute($value)
    {
        $this->attributes['content'] = htmlspecialchars($value);
    }

    public function getContentAttribute($value)
    {
        return htmlspecialchars_decode($value);
    }
}
