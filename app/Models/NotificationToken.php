<?php

namespace App\Models;

use App\Models\Base;
use Illuminate\Database\Eloquent\Model;

class NotificationToken extends Model
{
    use Base;
    protected $table = 'notification_token';
    protected $fillable = ['user_id', 'type', 'token', 'allow'];
}
