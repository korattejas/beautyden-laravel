<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFcmToken extends Model
{
    protected $fillable = [
        'user_id',
        'fcm_token',
        'device_type',
        'device_name',
        'ip_address',
    ];
}
