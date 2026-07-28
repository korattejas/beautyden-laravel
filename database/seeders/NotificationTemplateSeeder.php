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
                'message' => 'Hi {user_name}, welcome to BeautyDen! Get ready for luxurious salon services right at your doorstep. ✨',
                'type' => 'welcome'
            ],
            [
                'event_name' => 'order_placed',
                'title' => 'Booking Confirmed! ✅',
                'message' => 'Hi {user_name}, your booking #{order_id} has been placed successfully. Our expert beautician is getting ready for you! 🏡💆‍♀️',
                'type' => 'booking_detail'
            ],
            [
                'event_name' => 'order_assigned',
                'title' => 'Beautician Assigned 👩‍💼',
                'message' => 'Great news! An expert beautician has been assigned to your booking #{order_id} and is on the way. 🚗✨',
                'type' => 'booking_detail'
            ],
            [
                'event_name' => 'order_completed',
                'title' => 'Service Completed ✨',
                'message' => 'Hope you loved your BeautyDen experience! Please take a moment to leave a review for your booking #{order_id}. 🌸💖',
                'type' => 'add_review'
            ],
            [
                'event_name' => 'order_cancelled',
                'title' => 'Booking Cancelled ❌',
                'message' => 'Your booking #{order_id} has been cancelled. If you need any help, feel free to reach out to us! 📞',
                'type' => 'booking_detail'
            ],
            [
                'event_name' => 'wallet_added',
                'title' => 'Amount Credited 💰',
                'message' => 'Yay! ₹{amount} has been successfully added to your BeautyDen wallet. Use it on your next home service! 🌟',
                'type' => 'wallet_history'
            ],
            [
                'event_name' => 'referral_bonus',
                'title' => 'Referral Reward! 🎁',
                'message' => 'Your friend just joined BeautyDen! ₹{amount} cashback has been credited to your wallet. Keep sharing the glow! ✨',
                'type' => 'wallet_history'
            ],
            [
                'event_name' => 'payment_failed',
                'title' => 'Payment Failed ⚠️',
                'message' => 'Your payment for booking #{order_id} could not be completed. Please retry now to confirm your slot. 💳🔄',
                'type' => 'payment_status'
            ],
            [
                'event_name' => 'abandoned_cart',
                'title' => 'Forgot Something? 🛒✨',
                'message' => 'We noticed you left items in your cart! Complete your booking now and use code {coupon_code} to get 15% OFF instantly! 💃🌸',
                'type' => 'cart_reminder'
            ],
            [
                'event_name' => 'birthday_offer',
                'title' => 'Happy Birthday! 🎂🥳',
                'message' => 'Wishing you a fabulous day from BeautyDen! Here is a special birthday gift just for you. Use code {coupon_code} for an exclusive discount today! 🎁✨',
                'type' => 'offer_detail'
            ],
            [
                'event_name' => 'birthday_advance_7',
                'title' => 'Advance Birthday Treats! 🎂✨',
                'message' => 'Your birthday is in 7 days! 🥳 Start celebrating early with BeautyDen. Use code {coupon_code} to book your pamper session now! 💖',
                'type' => 'offer_detail'
            ],
            [
                'event_name' => 'birthday_advance_3',
                'title' => 'Your Birthday is coming up! 🎂💃',
                'message' => 'Only {days_left} left until your birthday! Book your home salon service now using code {coupon_code} to get a special discount. 🌸✨',
                'type' => 'offer_detail'
            ],
        ];

        // Clean up deleted templates from the database
        NotificationTemplate::where('event_name', 'birthday_remind_3')->delete();

        foreach ($templates as $template) {
            NotificationTemplate::updateOrCreate(
                ['event_name' => $template['event_name']],
                $template
            );
        }
    }
}
