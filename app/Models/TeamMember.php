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
        'photo',
        'certifications',
        'is_popular',
        'status',
    ];
}
