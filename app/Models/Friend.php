<?php

namespace App\Models;

use App\Models\Base;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    use Base;
    protected $table = 'friend';
    protected $fillable = ['user_id', 'friend_id'];

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function Friend()
    {
        return $this->belongsTo(User::class, 'friend_id', 'id');
    }
}
