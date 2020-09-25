<?php

namespace App\Models;

use App\Models\Base;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use Base;
    protected $table = 'user_profile';
    protected $fillable = ['user_id', 'profile_type', 'job_title', 'company', 'university'];
}
