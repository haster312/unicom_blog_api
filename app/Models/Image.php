<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Image extends Model
{
    use Base;
    protected $table = 'image';
    protected $fillable = ['main', 'thumbnail', 'mime', 'type'];
}
