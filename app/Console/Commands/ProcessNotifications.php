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

        $today = Carbon::today()->format('m-d');
        
        // Find users with birthday today
        $birthdayUsers = User::whereRaw("DATE_FORMAT(dob, '%m-%d') = ?", [$today])
            ->where('status', 1)
            ->get();

        foreach ($birthdayUsers as $user) {
            NotificationService::trigger($user->id, 'birthday_offer', [
                '{user_name}' => $user->name,
                '{coupon_code}' => 'BDAY50'
            ]);
            $this->info("Sent birthday wish to user #{$user->id}");
        }
    }

    /**
     * Process Abandoned Carts
     */
    private function processAbandonedCarts()
    {
        // Check for users who have items in cart updated exactly 2 hours ago
        // To prevent multiple sends, we only send when the cart was last updated between 120 and 125 mins ago
        $twoHoursAgoStart = Carbon::now()->subMinutes(125);
        $twoHoursAgoEnd = Carbon::now()->subMinutes(120);

        // Group by user_id to send one notification per user
        $abandonedUsers = \App\Models\Cart::whereBetween('updated_at', [$twoHoursAgoStart, $twoHoursAgoEnd])
            ->select('user_id')
            ->distinct()
            ->get();

        foreach ($abandonedUsers as $cart) {
            NotificationService::trigger($cart->user_id, 'abandoned_cart');
            $this->info("Sent abandoned cart reminder to user #{$cart->user_id}");
        }
    }
}
