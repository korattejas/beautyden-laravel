<?php

namespace App\Services;

use App\Models\NotificationTemplate;
use App\Models\Notification;
use App\Models\UserFcmToken;
use App\Helpers\FcmHelper;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Trigger a system event notification
     *
     * @param int $userId The ID of the user receiving the notification
     * @param string $eventName The unique code of the event (e.g., 'order_placed')
     * @param array $variables Array of variables to replace in the message (e.g., ['{user_name}' => 'John', '{order_id}' => '1234'])
     * @param string|null $referenceId The reference ID for deep-linking (e.g., the actual Order ID or Wallet ID)
     * @return bool True if successful, false otherwise
     */
    public static function trigger($userId, $eventName, $variables = [], $referenceId = null)
    {
        try {
            // 1. Fetch active template
            $template = NotificationTemplate::where('event_name', $eventName)
                ->where('status', 1)
                ->first();

            if (!$template) {
                // Template not found or inactive, do nothing
                return false;
            }

            // 2. Parse title and message with variables
            $title = self::parseVariables($template->title, $variables);
            $message = self::parseVariables($template->message, $variables);

            // 3. Save to database history
            $notification = Notification::create([
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'type' => $template->type,
                'reference_id' => $referenceId,
                'is_read' => 0
            ]);

            // 4. Send Push Notification via FCM
            $tokens = UserFcmToken::where('user_id', $userId)->pluck('fcm_token')->toArray();
            
            if (!empty($tokens)) {
                $customData = [
                    'type' => $template->type,
                    'reference_id' => $referenceId,
                    'notification_id' => $notification->id
                ];

                FcmHelper::sendPushNotification($tokens, $title, $message, null, $customData);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('NotificationService error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Replace variables in text
     */
    private static function parseVariables($text, $variables)
    {
        if (empty($variables)) return $text;

        // Ensure keys have curly braces, e.g., if passed as 'user_name' instead of '{user_name}'
        $parsedVariables = [];
        foreach ($variables as $key => $value) {
            $formattedKey = strpos($key, '{') === false ? '{' . $key . '}' : $key;
            $parsedVariables[$formattedKey] = $value;
        }

        return str_replace(array_keys($parsedVariables), array_values($parsedVariables), $text);
    }
}
