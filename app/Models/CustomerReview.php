<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'customer_name',
        'customer_photo',
        'rating',
        'review',
        'review_date',
        'helpful_count',
        'photos',
        'video',
        'is_popular',
        'status',
    ];

}
