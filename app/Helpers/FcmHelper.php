<?php

namespace App\Helpers;

class FcmHelper
{
    /**
     * Send Push Notification using FCM
     */
    public static function sendPushNotification($tokens, $title, $body, $image = null, $data = [])
    {
        $projectId = env('FIREBASE_PROJECT_ID');
        $credentialsPath = base_path(env('FIREBASE_CREDENTIALS'));

        if (!file_exists($credentialsPath) || !$projectId) {
            \Illuminate\Support\Facades\Log::error('Firebase credentials or Project ID missing');
            return ['success' => 0, 'failure' => count(is_array($tokens) ? $tokens : [$tokens]), 'message' => 'Firebase credentials missing'];
        }

        try {
            $credentials = new \Google\Auth\Credentials\ServiceAccountCredentials(
                ['https://www.googleapis.com/auth/firebase.messaging'],
                $credentialsPath
            );

            $authToken = $credentials->fetchAuthToken()['access_token'];
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error fetching Firebase auth token: ' . $e->getMessage());
            return ['success' => 0, 'failure' => count(is_array($tokens) ? $tokens : [$tokens]), 'message' => 'Auth token error'];
        }

        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $tokensArray = is_array($tokens) ? $tokens : [$tokens];
        
        $successCount = 0;
        $failureCount = 0;
        
        // Convert all values in $data to strings as required by FCM v1
        $dataMap = [];
        if (!empty($data)) {
            foreach ($data as $key => $val) {
                $dataMap[(string)$key] = (string)$val;
            }
        }

        foreach ($tokensArray as $fcmToken) {
            $payload = [
                'message' => [
                    'token' => $fcmToken,
                    'notification' => [
                        'title' => (string) $title,
                        'body' => (string) $body,
                    ]
                ]
            ];
            
            if ($image) {
                $payload['message']['notification']['image'] = $image;
            }
            
            if (!empty($dataMap)) {
                $payload['message']['data'] = (object)$dataMap;
            }

            $response = \Illuminate\Support\Facades\Http::withToken($authToken)->post($url, $payload);
            
            if ($response->successful()) {
                $successCount++;
            } else {
                $failureCount++;
                \Illuminate\Support\Facades\Log::error('FCM Send Error: ' . $response->body());
            }
        }

        return [
            'success' => $successCount,
            'failure' => $failureCount,
            'message' => "Successfully sent {$successCount}, failed {$failureCount}"
        ];
    }
}
