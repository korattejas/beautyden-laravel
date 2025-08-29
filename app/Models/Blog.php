<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'excerpt',
        'content',
        'read_time',
        'author',
        'publish_date',
        'tags',
        'icon',
        'featured',
        'status',
    ];
}
