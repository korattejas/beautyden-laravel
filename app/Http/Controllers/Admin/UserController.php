<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Appointment;
use App\Models\CouponUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class UserController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/UserController";
    }

    public function index()
    {
        return view('admin.user.index');
    }

    public function show($id)
    {
        try {
            $user = User::with(['addresses', 'subscriptions.plan' => function($q) {
                $q->withTrashed();
            }])->findOrFail($id);

            // Fetch appointments linked to this user's phone or email
            $appointments = Appointment::where('phone', $user->mobile_number)
                ->orWhere('email', $user->email)
                ->orderBy('appointment_date', 'desc')
                ->get();

            $total_appointments = $appointments->count();
            $total_coupons = CouponUsage::where('user_id', $user->id)->count();

            return view('admin.user.show', compact('user', 'appointments', 'total_appointments', 'total_coupons'));
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'show');
            return back()->with('error', 'User not found or error loading profile');
        }
    }

    public function getDataUser(Request $request)
    {
        try {
            if ($request->ajax()) {
                $users = User::query();

                if ($request->status !== null && $request->status !== '') {
                    $users->where('status', $request->status);
                }

                return DataTables::of($users)
                    ->addColumn('status', function ($row) {
                        return view('admin.render-view.datable-label', [
                            'status_array' => [
                                'is_simple_active' => 1,
                                'current_status' => $row->status
                            ]
                        ])->render();
                    })
                    ->addColumn('action', function ($row) {
                        return view('admin.render-view.datable-action', [
                            'action_array' => [
                                'is_simple_action' => 1,
                                'view_label' => 'View Profile',
                                'view_route' => route('admin.user.show', $row->id),
                                'delete_id' => $row->id,
                                'current_status' => $row->status,
                                'hidden_id' => $row->id,
                            ]
                        ])->render();
                    })
                    ->editColumn('created_at', function($row) {
                        return Carbon::parse($row->created_at)->format('d-m-Y H:i');
                    })
                    ->rawColumns(['action', 'status'])
                    ->make(true);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'getDataUser');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function changeStatus($id, $status)
    {
        try {
            User::where('id', $id)->update(['status' => $status]);
            return response()->json(['message' => trans('admin_string.msg_status_change')]);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function destroy(int $id)
    {
        try {
            $user = User::find($id);
            if ($user) {
                $user->delete();
                return response()->json(['message' => 'User deleted successfully']);
            }
            return response()->json(['error' => 'User not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }
}
