<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RazorpayTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_signature',
        'amount',
        'currency',
        'status',
        'method',
        'payment_details',
        'meta_data',
        'error_code',
        'error_description'
    ];

    protected $casts = [
        'payment_details' => 'array',
        'meta_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
