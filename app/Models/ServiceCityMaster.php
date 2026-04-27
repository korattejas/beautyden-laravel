<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCityMaster extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'category_id',
        'sub_category_id',
        'service_master_id',
        'price',
        'discount_price',
        'app_discount_percentage',
        'beautician_commission',
        'is_available',
        'status'
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function service()
    {
        return $this->belongsTo(ServiceMaster::class, 'service_master_id');
    }

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(ServiceSubcategory::class, 'sub_category_id');
    }
}
