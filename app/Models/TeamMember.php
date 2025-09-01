<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
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
        'is_popular',
        'status',
    ];
}
