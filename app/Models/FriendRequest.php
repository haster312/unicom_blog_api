<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FriendRequest extends Model
{
    use Base;
    protected $table = 'friend_request';
    protected $fillable = ['user_id', 'target_id'];

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function Target()
    {
        return $this->belongsTo(User::class, 'friend_id', 'id');
    }
}
