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
        'media_json',
        'is_popular',
        'is_new',
        'status',
    ];

    protected $casts = [
        'media_json' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
