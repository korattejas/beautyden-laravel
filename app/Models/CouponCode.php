<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponCode extends Model
{
    protected $fillable = [
        'user_ids',
        'code',
        'color_code',
        'discount_type',
        'discount_value',
        'min_purchase_amount',
        'max_discount_amount',
        'start_date',
        'end_date',
        'usage_limit',
        'usage_per_user',
        'is_first_order_only',
        'description',
        'status',
    ];

    protected $casts = [
        'user_ids' => 'array',
        'is_first_order_only' => 'boolean',
        'status' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function usages()
    {
        return $this->hasMany(CouponUsage::class, 'coupon_id');
    }
}
