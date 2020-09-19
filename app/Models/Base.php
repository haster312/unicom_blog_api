<?php
namespace App\Models;


trait Base
{
    public function getDateFormat()
    {
        return 'U';
    }

    public function getCreatedAtAttribute($value)
    {
        return strtotime($value);
    }

    public function getUpdatedAtAttribute($value)
    {
        return strtotime($value);
    }
}
