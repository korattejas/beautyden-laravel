<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'city_id',
        'service_category_id',
        'service_sub_category_id',
        'service_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'quantity',
        'price',
        'discount_price',
        'service_address',
        'appointment_date',
        'appointment_time',
        'special_notes',
        'assigned_to',
        'assigned_by',
        'services_data',
        'status',
        'company_amount',
    ];

    protected $casts = [
        'services_data' => 'array',
    ];
}
