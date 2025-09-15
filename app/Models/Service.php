<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
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
        'includes',
        'icon',
        'is_popular',
        'status',
    ];
}
