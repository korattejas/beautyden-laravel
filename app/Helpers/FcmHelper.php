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

        $tokensArray = array_values(array_filter(array_unique(is_array($tokens) ? $tokens : [$tokens])));
        
        $successCount = 0;
        $failureCount = 0;
        
        // Convert all values in $data to strings as required by FCM v1
        $dataMap = [];
        if (!empty($data)) {
            foreach ($data as $key => $val) {
                $dataMap[(string)$key] = (string)$val;
            }
        }

        // Fetch user details for the tokens to make the logs highly informative
        $tokenUserMap = \App\Models\UserFcmToken::with('user')
            ->whereIn('fcm_token', $tokensArray)
            ->get()
            ->keyBy('fcm_token');

        // Chunk tokens into batches of 50 to send concurrently and avoid timeouts
        $chunks = array_chunk($tokensArray, 50);

        foreach ($chunks as $chunk) {
            $responses = \Illuminate\Support\Facades\Http::pool(function (\Illuminate\Http\Client\Pool $pool) use ($chunk, $authToken, $url, $title, $body, $image, $dataMap) {
                $poolRequests = [];
                foreach ($chunk as $fcmToken) {
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
                    
                    $poolRequests[] = $pool->withToken($authToken)->post($url, $payload);
                }
                return $poolRequests;
            });

            foreach ($chunk as $index => $fcmToken) {
                $response = $responses[$index] ?? null;
                
                // Get user info for log
                $userInfo = "Unknown User";
                if (isset($tokenUserMap[$fcmToken]) && $tokenUserMap[$fcmToken]->user) {
                    $user = $tokenUserMap[$fcmToken]->user;
                    $userInfo = "User ID: {$user->id} | Name: {$user->name} | Mobile: {$user->mobile_number}";
                }

                if ($response instanceof \Illuminate\Http\Client\Response && $response->successful()) {
                    $successCount++;
                    \Illuminate\Support\Facades\Log::info("FCM Send Success | Token: {$fcmToken} | {$userInfo}");
                } else {
                    $failureCount++;
                    $errorMessage = 'Request / connection failed';
                    if ($response instanceof \Illuminate\Http\Client\Response) {
                        $errorMessage = $response->body();
                    }
                    \Illuminate\Support\Facades\Log::error("FCM Send Failure | Token: {$fcmToken} | {$userInfo} | Error: {$errorMessage}");
                }
            }
        }

        return [
            'success' => $successCount,
            'failure' => $failureCount,
            'message' => "Successfully sent {$successCount}, failed {$failureCount}"
        ];
    }
}
