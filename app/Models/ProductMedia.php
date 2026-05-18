<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMedia extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'type', 'file_path', 'is_main', 'status'];

    public function product()
    {
        return $this->belongsTo(ProductItem::class, 'product_id');
    }
}
