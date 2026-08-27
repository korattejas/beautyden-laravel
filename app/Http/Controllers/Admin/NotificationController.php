<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PushNotification;
use App\Models\UserFcmToken;
use App\Helpers\FcmHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;

class NotificationController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/NotificationController";
    }

    public function index()
    {
        return view('admin.notifications.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = PushNotification::query();
            return DataTables::of($data)
                ->addColumn('status', function ($item) {
                    if ($item->is_sent == 1) return '<span class="badge bg-success">Sent</span>';
                    if ($item->is_sent == 2) return '<span class="badge bg-danger">Failed</span>';
                    return '<span class="badge bg-warning">Pending</span>';
                })
                ->addColumn('action', function($item) {
                    $logUrl = route('admin.notifications.log', $item->id);
                    $btn = '<a href="'.$logUrl.'" target="_blank" class="btn btn-sm btn-info me-1"><i class="fa fa-file-text"></i> Log</a>';
                    $btn .= '<button class="btn btn-sm btn-primary" onclick="resendNotification('.$item->id.')"><i class="fa fa-refresh"></i> Resend</button>';
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
    }

    public function resend($id)
    {
        try {
            $notification = PushNotification::findOrFail($id);
            
            // Reset counts and set status to unsent to allow re-sending
            $notification->update([
                'is_sent' => 0,
                'success_count' => 0,
                'failure_count' => 0
            ]);

            // Resend now
            $customData = $notification->custom_data ? (array)$notification->custom_data : [];
            $specificUsers = null;
            if ($notification->target_type == 'specific') {
                $specificUsers = $customData['_specific_users'] ?? null;
            }
            
            $this->sendNotificationNow($notification, $specificUsers);

            return response()->json(['success' => true, 'message' => 'Notification resent successfully']);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'resend');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        $users = \App\Models\User::where('status', 1)->get();
        return view('admin.notifications.create', compact('users'));
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'message' => 'required|string',
                'image' => 'nullable|image',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
            }

            $imageName = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/notifications'), $imageName);
            }

            $customData = $request->custom_data ? (array)json_decode($request->custom_data) : [];
            if ($request->target_type == 'specific') {
                $customData['_specific_users'] = implode(',', $request->specific_users ?? []);
            }

            $notification = PushNotification::create([
                'title' => $request->title,
                'message' => $request->message,
                'image' => $imageName ? asset('uploads/notifications/' . $imageName) : null,
                'target_type' => $request->target_type ?? 'all',
                'custom_data' => !empty($customData) ? $customData : null,
                'scheduled_at' => $request->scheduled_at,
                'is_sent' => 0
            ]);

            // If not scheduled, send immediately
            if (!$request->scheduled_at) {
                $specificUsers = $request->target_type == 'specific' ? implode(',', $request->specific_users ?? []) : null;
                $this->sendNotificationNow($notification, $specificUsers);
            }

            return response()->json(['success' => true, 'message' => 'Notification processed successfully']);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'store');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    private function sendNotificationNow($notification, $specificUsers = null)
    {
        $tokensQuery = UserFcmToken::query();
        
        if ($notification->target_type == 'specific' && !empty($specificUsers)) {
            $tokensQuery->whereIn('user_id', explode(',', $specificUsers));
        } elseif ($notification->target_type == 'customers') {
            $tokensQuery->whereHas('user', function($q) {
                $q->where('role', '!=', 2); // Anyone who is not a beautician is a customer
            });
        } elseif ($notification->target_type == 'beauticians') {
            $tokensQuery->whereHas('user', function($q) {
                $q->where('role', 2);
            });
        }

        $tokens = $tokensQuery->distinct()->pluck('fcm_token')->filter()->toArray();
        
        if (empty($tokens)) {
            \Illuminate\Support\Facades\Log::warning("Notification failed: No FCM tokens found for the selected users.");
            $notification->update(['is_sent' => 2, 'failure_count' => 1]); // Mark failed if no tokens
            return;
        }

        $customDataArray = is_string($notification->custom_data) ? json_decode($notification->custom_data, true) : (array)$notification->custom_data;

        $response = FcmHelper::sendPushNotification(
            $tokens,
            $notification->title,
            $notification->message,
            $notification->image,
            $customDataArray,
            $notification->id
        );

        if (isset($response['success']) && $response['success'] > 0) {
            $notification->update([
                'is_sent' => 1,
                'success_count' => $response['success'],
                'failure_count' => $response['failure'] ?? 0
            ]);
        } else {
            $notification->update([
                'is_sent' => 2,
                'failure_count' => $response['failure'] ?? count($tokens)
            ]);
            \Illuminate\Support\Facades\Log::error("Notification failed completely.", ['response' => $response]);
        }
    }

    public function showLog($id)
    {
        $logPath = storage_path("logs/notifications/notification_{$id}.log");
        if (File::exists($logPath)) {
            $content = File::get($logPath);
            return response($content, 200)->header('Content-Type', 'text/plain; charset=UTF-8');
        }
        return response('No detailed logs found for this notification yet.', 404);
    }
}
