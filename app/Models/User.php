<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use App\Models\Base;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, Base;
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'username', 'email', 'password', 'social', 'social_token', 'social_id',
        'university_id', 'course_id', 'avatar_id', 'bio'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value . getHash());
    }

    public function Avatar()
    {
        return $this->belongsTo(Image::class, 'avatar_id', 'id');
    }

    public function University()
    {
        return $this->belongsTo(University::class, 'university_id', 'id');
    }

    public function Course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

    public function Article()
    {
        return $this->hasMany(Article::class, 'author_id', 'id');
    }
}
