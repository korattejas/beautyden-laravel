<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Database\Eloquent\Model;

class Admin extends Authenticatable
{
    protected $guard_name = 'admin';

    protected $guarded = [];

    use HasFactory;

    protected $fillable = [
        'name',
        'mobile_number',
        'email',
        'password',
        'status',
    ];
}
