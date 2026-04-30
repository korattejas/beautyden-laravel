<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model
{
    use HasFactory;

    protected $table = 'push_notifications';

    protected $fillable = [
        'title',
        'message',
        'image',
        'target_type',
        'custom_data',
        'scheduled_at',
        'is_sent',
        'success_count',
        'failure_count',
    ];

    protected $casts = [
        'custom_data' => 'array',
        'scheduled_at' => 'datetime',
    ];
}
