<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use Base;
    protected $table = 'image';
    protected $fillable = ['main', 'thumbnail', 'mime', 'type'];
}
