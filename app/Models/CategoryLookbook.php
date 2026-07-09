<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryLookbook extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'photos',
        'status',
    ];

    protected $casts = [
        'photos' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id', 'id');
    }
}
