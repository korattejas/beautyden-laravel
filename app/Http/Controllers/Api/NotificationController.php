<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Exception;

class NotificationController extends Controller
{
    protected mixed $success_status, $exception_status, $validation_error_status, $common_error_message;
    protected string $controller_name;

    public function __construct()
    {
        $this->controller_name = 'API/NotificationController';
        $this->success_status = config('custom.status_code_for_success', 200);
        $this->exception_status = config('custom.status_code_for_exception_error', 500);
        $this->validation_error_status = config('custom.status_code_for_validation_error', 422);
        $this->common_error_message = config('custom.common_error_message', 'Something went wrong.');
    }

    /**
     * Send test notification to Flutter developer using FCM HTTP v1 API
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function sendTestNotification(Request $request): JsonResponse
    {
        $function_name = 'sendTestNotification';
        try {
            $validator = Validator::make($request->all(), [
                'fcm_token' => 'required|string',
                'title' => 'required|string|max:255',
                'body' => 'required|string',
                'data' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            $projectId = env('FIREBASE_PROJECT_ID');
            $credentialsPath = base_path(env('FIREBASE_CREDENTIALS'));

            if (!file_exists($credentialsPath)) {
                return $this->sendError("Firebase Service Account JSON file not found at: " . env('FIREBASE_CREDENTIALS'), $this->exception_status);
            }

            if (!$projectId) {
                return $this->sendError('FIREBASE_PROJECT_ID is missing in .env file.', $this->exception_status);
            }

            // Get OAuth2 Access Token using Google Auth Library
            $credentials = new ServiceAccountCredentials(
                ['https://www.googleapis.com/auth/firebase.messaging'],
                $credentialsPath
            );

            $token = $credentials->fetchAuthToken()['access_token'];

            $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

            $payload = [
                "message" => [
                    "token" => $request->fcm_token,
                    "notification" => [
                        "title" => $request->title,
                        "body" => $request->body
                    ]
                ]
            ];

            if (!empty($request->data)) {
                // Ensure all data values are strings for FCM v1 and it's an associative array
                $dataMap = [];
                foreach ($request->data as $key => $value) {
                    $dataMap[(string)$key] = (string)$value;
                }
                $payload['message']['data'] = $dataMap;
            }

            $response = Http::withToken($token)->post($url, $payload);

            if ($response->successful()) {
                return $this->sendResponse($response->json(), 'Notification sent successfully using FCM v1.', $this->success_status);
            } else {
                logError($this->controller_name, $function_name, [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                $errorData = $response->json();
                $errorMessage = $errorData['error']['message'] ?? 'Unknown Firebase Error';
                
                return $this->sendError("Firebase API Error: " . $errorMessage, $response->status());
            }

        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    /**
     * Get user notifications list
     */
    public function getNotifications(Request $request): JsonResponse
    {
        $function_name = 'getNotifications';
        try {
            $user = auth('user')->user();
            if (!$user) {
                return $this->sendError('Unauthorised.', $this->validation_error_status);
            }

            $page = $request->input('page', 1);
            $limit = $request->input('limit', 15);

            $type = $request->input('type');

            $query = Notification::where('user_id', $user->id);
            
            if ($type && strtolower($type) !== 'all') {
                $requestedType = strtolower($type);
                
                // Map frontend tabs to backend template types
                if ($requestedType === 'bookings') {
                    $query->whereIn('type', ['booking_detail', 'payment_status', 'add_review', 'cart_reminder']);
                } elseif ($requestedType === 'wallet') {
                    $query->whereIn('type', ['wallet_history']);
                } elseif ($requestedType === 'offers') {
                    $query->whereIn('type', ['offer_detail', 'offer_list']);
                } elseif ($requestedType === 'updates') {
                    $query->whereIn('type', ['welcome', 'general']);
                } else {
                    $query->where('type', $requestedType);
                }
            }

            $notifications = $query->orderBy('created_at', 'desc')
                ->paginate($limit, ['*'], 'page', $page);

            $data = [
                'tabs' => [
                    ['id' => 'All', 'name' => 'All'],
                    ['id' => 'Bookings', 'name' => 'Bookings'],
                    ['id' => 'Wallet', 'name' => 'Wallet'],
                    ['id' => 'Offers', 'name' => 'Offers'],
                    ['id' => 'Updates', 'name' => 'Updates'],
                ],
                'list' => $notifications->items(),
                'total_pages' => $notifications->lastPage(),
                'current_page' => $notifications->currentPage(),
                'total_records' => $notifications->total(),
            ];

            return $this->sendResponse($data, 'Notifications retrieved successfully.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    /**
     * Mark notification(s) as read
     */
    public function markAsRead(Request $request): JsonResponse
    {
        $function_name = 'markAsRead';
        try {
            $user = auth()->user();
            if (!$user) {
                return $this->sendError('Unauthorised.', $this->validation_error_status);
            }

            $notificationId = $request->input('notification_id');

            if ($notificationId) {
                // Mark single notification
                Notification::where('user_id', $user->id)
                    ->where('id', $notificationId)
                    ->update(['is_read' => 1]);
            } else {
                // Mark all notifications
                Notification::where('user_id', $user->id)
                    ->update(['is_read' => 1]);
            }

            return $this->sendResponse((object)[], 'Notifications marked as read.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    /**
     * Clear all user notifications
     */
    public function clearAll(Request $request): JsonResponse
    {
        $function_name = 'clearAll';
        try {
            $user = auth()->user();
            if (!$user) {
                return $this->sendError('Unauthorised.', $this->validation_error_status);
            }

            Notification::where('user_id', $user->id)->delete();

            return $this->sendResponse((object)[], 'All notifications cleared.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }
}
