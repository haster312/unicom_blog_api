<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class University extends Model
{
    use Base;
    protected $table = 'university';
    protected $fillable = ['name'];

}
