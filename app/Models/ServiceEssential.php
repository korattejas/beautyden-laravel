<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceEssential extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'icon',
        'type',
        'status',
    ];
}
