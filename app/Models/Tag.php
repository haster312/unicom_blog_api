<?php

namespace App\Models;

use App\Models\Base;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use Base;
    protected $table = 'tag';
    protected $fillable = ['name', 'count'];
}
