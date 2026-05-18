<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'variant_name', 'price', 'discount_percentage', 'stock_quantity', 'status'];

    public function product()
    {
        return $this->belongsTo(ProductItem::class, 'product_id');
    }
}
