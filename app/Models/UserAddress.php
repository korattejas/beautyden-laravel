<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $fillable = [
        'user_id',
        'city_id',
        'address',
        'home_number',
        'street_address',
        'landmark',
        'city_village_name',
        'state_name',
        'pincode',
        'latitude',
        'longitude',
        'type',
        'is_default',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
