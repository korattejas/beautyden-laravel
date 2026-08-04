<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $table = 'cities';

    protected $fillable = [
        'name',
        'state',
        'area',
        'slug',
        'icon',
        'latitude',
        'longitude',
        'radius_km',
        'launch_quarter',
        'is_popular',
        'status',
    ];
}
