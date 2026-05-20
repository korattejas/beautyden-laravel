<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceMasterVariant extends Model
{
    protected $fillable = [
        'service_master_id',
        'name',
        'price',
        'duration',
    ];

    public function service_master()
    {
        return $this->belongsTo(ServiceMaster::class, 'service_master_id');
    }
}
