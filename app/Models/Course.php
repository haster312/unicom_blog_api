<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use Base;
    protected $table = 'course';
    protected $fillable = ['name', 'slug', 'university_id'];

    public function University()
    {
        return $this->belongsTo(University::class, 'university_id', 'id');
    }
}
