<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOrder extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'total_amount', 'payment_status', 'order_status', 'address', 'order_data', 'status'];

    protected $casts = [
        'order_data' => 'array',
    ];
}
