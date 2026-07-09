<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCombo extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'service_type_id', 'description', 'image', 'min_price', 'status'];

    public function items()
    {
        return $this->hasMany(ServiceComboItem::class, 'combo_id');
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id');
    }
}
