<?php

namespace App\Helpers;

class FcmHelper
{
    /**
     * Send Push Notification using FCM
     */
    public static function sendPushNotification($tokens, $title, $body, $image = null, $data = [])
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $serverKey = env('FCM_SERVER_KEY'); // You need to add this in .env

        if (empty($tokens)) return ['success' => false, 'message' => 'No tokens provided'];

        $notification = [
            'title' => $title,
            'body' => $body,
            'image' => $image,
            'sound' => 'default',
            'badge' => '1',
        ];

        $payload = [
            'registration_ids' => is_array($tokens) ? $tokens : [$tokens],
            'notification' => $notification,
            'data' => $data, // Custom data for redirection in app
            'priority' => 'high',
        ];

        $headers = [
            'Authorization: key=' . $serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }
}
