<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'offer_short_description',
        'position',
        'media_type',
        'media',
        'video_thumbnail',
        'link',
        'priority',
        'status',
    ];

    protected $casts = [
        'media' => 'array',
        'status' => 'integer',
        'priority' => 'integer',
    ];
}
