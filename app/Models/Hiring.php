<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hiring extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'city',
        'min_experience',
        'max_experience',
        'salary_range',
        'experience_level',
        'hiring_type',
        'gender_preference',
        'required_skills',
        'is_popular',
        'status',
    ];

}
