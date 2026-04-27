<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceMaster extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'sub_category_id',
        'name',
        'skin_type',
        'price',
        'discount_price',
        'duration',
        'rating',
        'reviews',
        'description',
        'icon',
        'banner_media',   // Media carousel
        'before_after',   // Before/After pairs
        'content_json',   // Dynamic sections
        'is_popular',
        'status',
    ];

    protected $casts = [
        'banner_media' => 'array',
        'before_after' => 'array',
        'content_json' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(ServiceSubcategory::class, 'sub_category_id');
    }
}
