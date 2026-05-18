<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'brand_id', 'category_id', 'sub_category_id', 'name', 'slug',
        'short_description', 'description', 'price', 'discount_percentage',
        'sku', 'stock_quantity', 'is_featured', 'is_new', 'content_json',
        'show_in_client_app', 'status'
    ];

    protected $casts = [
        'content_json' => 'array',
    ];

    public function brand()
    {
        return $this->belongsTo(ProductBrand::class, 'brand_id');
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(ProductSubCategory::class, 'sub_category_id');
    }

    public function media()
    {
        return $this->hasMany(ProductMedia::class, 'product_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }
}
