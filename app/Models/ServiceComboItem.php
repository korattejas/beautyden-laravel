<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceComboItem extends Model
{
    protected $fillable = ['combo_id', 'service_master_id', 'variant_id', 'is_default'];

    public function service()
    {
        return $this->belongsTo(ServiceMaster::class, 'service_master_id');
    }

    public function variant()
    {
        return $this->belongsTo(ServiceMasterVariant::class, 'variant_id');
    }
}
