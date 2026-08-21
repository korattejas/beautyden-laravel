<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PushNotification;
use App\Models\UserFcmToken;
use App\Models\User;
use App\Helpers\FcmHelper;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProcessNotifications extends Command
{
    protected $signature = 'notifications:process';
    protected $description = 'Process scheduled push notifications and automatic events like birthdays';

    public function handle()
    {
        $this->info('Starting notification processing...');
        
        $this->processScheduledNotifications();
        $this->processBirthdays();
        $this->processAbandonedCarts();

        $this->info('Notification processing completed.');
    }

    /**
     * Process manually scheduled Push Notifications (admin campaigns)
     */
    private function processScheduledNotifications()
    {
        // Get notifications scheduled for now or in the past that haven't been sent yet
        $notifications = PushNotification::where('is_sent', 0)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', Carbon::now())
            ->get();

        foreach ($notifications as $notification) {
            try {
                $tokensQuery = UserFcmToken::query();
                
                $customData = is_string($notification->custom_data) ? json_decode($notification->custom_data, true) : (array)$notification->custom_data;
                
                if ($notification->target_type == 'specific') {
                    $specificUsersStr = $customData['_specific_users'] ?? '';
                    if (!empty($specificUsersStr)) {
                        $tokensQuery->whereIn('user_id', explode(',', $specificUsersStr));
                    }
                } elseif ($notification->target_type == 'customers') {
                    $tokensQuery->whereHas('user', function($q) {
                        $q->where('role', '!=', 2)->where('status', 1);
                    });
                } elseif ($notification->target_type == 'beauticians') {
                    $tokensQuery->whereHas('user', function($q) {
                        $q->where('role', 2)->where('status', 1);
                    });
                }

                // Remove the internal _specific_users key before sending to FCM
                if (isset($customData['_specific_users'])) {
                    unset($customData['_specific_users']);
                }

                $tokens = $tokensQuery->pluck('fcm_token')->toArray();

                if (!empty($tokens)) {
                    $response = FcmHelper::sendPushNotification(
                        $tokens,
                        $notification->title,
                        $notification->message,
                        $notification->image,
                        $customData
                    );
                    
                    if (isset($response['success']) && $response['success'] > 0) {
                        $notification->update([
                            'is_sent' => 1,
                            'success_count' => $response['success'],
                            'failure_count' => $response['failure'] ?? 0
                        ]);
                        $this->info("Sent scheduled notification #{$notification->id}");
                    } else {
                        $notification->update([
                            'is_sent' => 2,
                            'failure_count' => $response['failure'] ?? count($tokens)
                        ]);
                        $this->error("Failed to send scheduled notification #{$notification->id}");
                        Log::error("Scheduled Notification failed.", ['response' => $response]);
                    }
                } else {
                    // No tokens found
                    $notification->update(['is_sent' => 2, 'failure_count' => 1]);
                    $this->info("No tokens found for scheduled notification #{$notification->id}");
                    Log::warning("Scheduled Notification failed: No tokens found for notification #{$notification->id}");
                }

            } catch (\Exception $e) {
                Log::error('Process scheduled notifications error: ' . $e->getMessage());
                // Mark as failed
                $notification->update(['is_sent' => 2]);
            }
        }
    }

    /**
     * Process daily birthday automated notifications
     */
    private function processBirthdays()
    {
        if (Carbon::now()->format('H:i') !== '09:00') {
            return;
        }

        $this->info("Processing daily birthdays...");

        // Fetch BDAY50 coupon to check if user has used it
        $bdayCoupon = \App\Models\CouponCode::where('code', 'BDAY50')->first();
        $bdayCouponId = $bdayCoupon ? $bdayCoupon->id : null;

        // Birthday offsets and their corresponding templates
        $schedules = [
            7  => 'birthday_advance_7',
            3  => 'birthday_advance_3',
            2  => 'birthday_advance_3',
            1  => 'birthday_advance_3',
            0  => 'birthday_offer',
        ];

        foreach ($schedules as $days => $eventName) {
            // Check users born on today + days offset
            $targetDate = Carbon::today()->addDays($days)->format('m-d');
            
            $users = User::whereRaw("DATE_FORMAT(dob, '%m-%d') = ?", [$targetDate])
                ->where('status', 1)
                ->get();

            foreach ($users as $user) {
                // If the coupon exists and the user has already used it, skip notifications
                if ($bdayCouponId) {
                    $hasUsed = \App\Models\CouponUsage::where('coupon_id', $bdayCouponId)
                        ->where('user_id', $user->id)
                        ->exists();
                    if ($hasUsed) {
                        continue;
                    }
                }

                $daysLeftText = $days . ($days == 1 ? ' day' : ' days');

                NotificationService::trigger($user->id, $eventName, [
                    '{user_name}' => $user->name,
                    '{coupon_code}' => 'BDAY50',
                    '{days_left}' => $daysLeftText
                ]);
                $this->info("Sent birthday notification ({$eventName}) to user #{$user->id} (offset: {$days} days)");
            }
        }
    }

    /**
     * Process Abandoned Carts
     */
    private function processAbandonedCarts()
    {
        // Get all unique users who have items in their cart
        $userIdsWithCart = \App\Models\Cart::select('user_id')
            ->distinct()
            ->pluck('user_id');

        foreach ($userIdsWithCart as $userId) {
            // Find the latest updated_at for this user's cart
            $latestUpdate = \App\Models\Cart::where('user_id', $userId)->max('updated_at');
            
            if ($latestUpdate) {
                $latestUpdate = Carbon::parse($latestUpdate);
                $diffInMinutes = $latestUpdate->diffInMinutes(Carbon::now());
                
                // If the cart's latest update was between 2 to 3 hours ago (120 - 180 mins)
                if ($diffInMinutes >= 120 && $diffInMinutes <= 180) {
                    $cacheKey = 'abandoned_cart_notified_' . $userId;
                    
                    // Ensure we only send one notification per user every 24 hours
                    if (!\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                        NotificationService::trigger($userId, 'abandoned_cart', [
                            '{coupon_code}' => 'SAVE250'
                        ]);
                        \Illuminate\Support\Facades\Cache::put($cacheKey, true, now()->addHours(24));
                        $this->info("Sent abandoned cart reminder to user #{$userId}");
                    }
                }
            }
        }
    }
}
