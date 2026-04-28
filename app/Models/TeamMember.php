<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'id_number',
        'phone',
        'dob',
        'blood_group',
        'role',
        'experience_years',
        'specialties',
        'bio',
        'icon',
        'certifications',
        'state',
        'city',
        'taluko',
        'village',
        'address',
        'latitude',
        'longitude',
        'is_popular',
        'status',
    ];
}
