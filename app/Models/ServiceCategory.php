<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_type_id',
        'name',
        'description',
        'icon',
        'is_popular',
        'is_new',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
