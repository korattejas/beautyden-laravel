<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCityVariantPrice extends Model
{
    protected $fillable = [
        'city_id',
        'service_master_id',
        'variant_id',
        'price',
        'discount_price',
    ];
}
