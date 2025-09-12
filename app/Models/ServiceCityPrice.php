<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCityPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'category_id',
        'service_id',
        'price',
        'discount_price',
        'sttaus'
    ];
}
