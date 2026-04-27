<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'appointment_id',
        'category_id',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(ServiceMaster::class, 'service_id');
    }
}
