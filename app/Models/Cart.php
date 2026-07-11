<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_master_id',
        'variant_id',
        'qty',
        'city_id',
    ];

    public function service()
    {
        return $this->belongsTo(ServiceMaster::class, 'service_master_id');
    }

    public function variant()
    {
        return $this->belongsTo(ServiceMasterVariant::class, 'variant_id');
    }
}
