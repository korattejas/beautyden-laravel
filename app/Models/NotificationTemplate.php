<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_name',
        'title',
        'message',
        'type',
        'status',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getScreenTypes()
    {
        return [
            'welcome' => 'Home Page (Welcome)',
            'booking_detail' => 'Booking Details',
            'wallet_history' => 'Wallet History',
            'offer_detail' => 'Offer / Coupon Details',
            'offer_list' => 'Offers List',
            'add_review' => 'Add Review Screen',
            'cart_reminder' => 'Cart / Checkout',
            'payment_status' => 'Retry Payment / Booking',
            'general' => 'General (Popup, No Redirect)'
        ];
    }
}
