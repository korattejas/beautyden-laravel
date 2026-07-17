<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NotificationTemplate;

class NotificationTemplateSeeder extends Seeder
{
    public function run()
    {
        $templates = [
            [
                'event_name' => 'welcome',
                'title' => 'Welcome! 🎉',
                'message' => 'Welcome to BeautyDen! Get ready for premium services.',
                'type' => 'welcome'
            ],
            [
                'event_name' => 'order_placed',
                'title' => 'Booking Confirmed! ✅',
                'message' => 'Hi {user_name}, your booking #{order_id} has been placed successfully.',
                'type' => 'booking_detail'
            ],
            [
                'event_name' => 'order_assigned',
                'title' => 'Beautician Assigned 👩‍💼',
                'message' => 'A beautician has been assigned to your booking #{order_id}.',
                'type' => 'booking_detail'
            ],
            [
                'event_name' => 'order_completed',
                'title' => 'Service Completed ✨',
                'message' => 'How was your experience? Please leave a review for your booking #{order_id}!',
                'type' => 'add_review'
            ],
            [
                'event_name' => 'order_cancelled',
                'title' => 'Booking Cancelled ❌',
                'message' => 'Your booking #{order_id} has been cancelled.',
                'type' => 'booking_detail'
            ],
            [
                'event_name' => 'wallet_added',
                'title' => 'Amount Credited 💰',
                'message' => '₹{amount} has been added to your wallet.',
                'type' => 'wallet_history'
            ],
            [
                'event_name' => 'referral_bonus',
                'title' => 'Referral Reward! 🎁',
                'message' => 'Your friend joined! ₹{amount} cashback credited to your wallet.',
                'type' => 'wallet_history'
            ],
            [
                'event_name' => 'payment_failed',
                'title' => 'Payment Failed ⚠️',
                'message' => 'Your payment for booking #{order_id} failed. Please retry to confirm booking.',
                'type' => 'payment_status'
            ],
            [
                'event_name' => 'abandoned_cart',
                'title' => 'Forgot Something? 🛒',
                'message' => 'You left items in your cart. Complete your booking now!',
                'type' => 'cart_reminder'
            ],
            [
                'event_name' => 'birthday_offer',
                'title' => 'Happy Birthday! 🎂',
                'message' => 'Here is a special gift just for you today. Apply code {coupon_code}.',
                'type' => 'offer_detail'
            ],
        ];

        foreach ($templates as $template) {
            NotificationTemplate::updateOrCreate(
                ['event_name' => $template['event_name']],
                $template
            );
        }
    }
}
