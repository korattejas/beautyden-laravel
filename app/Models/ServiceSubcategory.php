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
        'description',
        'is_popular',
        'status',
    ];

    /**
     * Relation with ServiceCategory
     */
    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }
}
