<?php

namespace App\Models;

use App\Models\Base;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use Base;
    protected $table = 'category';
    protected $fillable = ['name', 'slug', 'order'];
}
