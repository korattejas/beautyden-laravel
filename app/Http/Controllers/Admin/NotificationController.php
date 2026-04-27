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
                    return '<button class="btn btn-sm btn-primary" onclick="resendNotification('.$item->id.')">Resend</button>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
    }

    public function create()
    {
        return view('admin.notifications.create');
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

            $notification = PushNotification::create([
                'title' => $request->title,
                'message' => $request->message,
                'image' => $imageName ? asset('uploads/notifications/' . $imageName) : null,
                'target_type' => $request->target_type ?? 'all',
                'custom_data' => $request->custom_data ? json_decode($request->custom_data) : null,
                'scheduled_at' => $request->scheduled_at,
                'is_sent' => 0
            ]);

            // If not scheduled, send immediately
            if (!$request->scheduled_at) {
                $this->sendNotificationNow($notification);
            }

            return response()->json(['success' => true, 'message' => 'Notification processed successfully']);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'store');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    private function sendNotificationNow($notification)
    {
        $tokensQuery = UserFcmToken::query();
        // Here you can add targeting logic if needed
        $tokens = $tokensQuery->pluck('fcm_token')->toArray();
        
        $response = FcmHelper::sendPushNotification(
            $tokens,
            $notification->title,
            $notification->message,
            $notification->image,
            (array)$notification->custom_data
        );

        if (isset($response['success']) && $response['success'] > 0) {
            $notification->update([
                'is_sent' => 1,
                'success_count' => $response['success'],
                'failure_count' => $response['failure'] ?? 0
            ]);
        } else {
            $notification->update(['is_sent' => 2]);
        }
    }
}
