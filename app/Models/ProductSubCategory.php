<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSubCategory extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'name', 'is_featured', 'is_new', 'status'];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }
}
