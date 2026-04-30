<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceSubcategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_category_id',
        'name',
        'icon',
        'media_json',
        'description',
        'starting_at_price',
        'is_popular',
        'status',
    ];

    protected $casts = [
        'media_json' => 'array',
    ];

    /**
     * Relation with ServiceCategory
     */
    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }
}
