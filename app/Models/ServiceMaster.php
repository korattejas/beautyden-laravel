<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceMaster extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'sub_category_id',
        'name',
        'price',
        'discount_price',
        'duration',
        'rating',
        'reviews',
        'description',
        'icon',
        'content_json', // Unified dynamic content
        'is_popular',
        'status',
    ];

    protected $casts = [
        'content_json' => 'array',
    ];
}
