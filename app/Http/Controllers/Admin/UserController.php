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

    public function index(Request $request)
    {
        $query = User::query();

        $totalUsers = (clone $query)->count();
        $activeUsers = (clone $query)->where('status', 1)->count();
        $suspendedUsers = (clone $query)->where('status', 0)->count();
        $appUsers = (clone $query)->where('role', 1)->count(); // User
        $webUsers = (clone $query)->where('role', 2)->count(); // Beautician

        return view('admin.user.index', compact(
            'totalUsers',
            'activeUsers',
            'suspendedUsers',
            'appUsers',
            'webUsers'
        ));
    }

    public function show($id)
    {
        // kept for backward compatibility if needed
    }

    public function view($id)
    {
        try {
            $user = User::with(['addresses', 'subscriptions.plan' => function($q) {
                $q->withTrashed();
            }])->findOrFail($id);

            $cityName = \App\Models\City::where('id', $user->city_id)->value('name');
            $user->city_name = $cityName;

            // Fetch appointments linked to this user's ID primarily, with fallback to phone/email
            $appointments = Appointment::where(function($q) use ($user) {
                $q->where('user_id', $user->id);
                if (!empty($user->mobile_number)) {
                    $q->orWhere('phone', $user->mobile_number);
                }
                if (!empty($user->email)) {
                    $q->orWhere('email', $user->email);
                }
            })
            ->orderBy('appointment_date', 'desc')
            ->get();

            $total_appointments = $appointments->count();
            $total_coupons = CouponUsage::where('user_id', $user->id)->count();
            $total_spent = $appointments->where('status', 3)->sum(function ($appointment) {
                return (float) ($appointment->services_data['summary']['grand_total'] ?? 0);
            });

            $wallet_transactions = \App\Models\WalletTransaction::where('user_id', $user->id)
                ->orderBy('id', 'desc')
                ->get();
                
            $referred_by_user = null;
            if (!empty($user->referred_by)) {
                $referred_by_user = User::where('referral_code', $user->referred_by)->select('id', 'name', 'mobile_number', 'referral_code')->first();
            }
            
            $total_referrals_made = 0;
            if (!empty($user->referral_code)) {
                $total_referrals_made = User::where('referred_by', $user->referral_code)->count();
            }

            return response()->json([
                'data' => [
                    'user' => $user,
                    'total_appointments' => $total_appointments,
                    'total_coupons' => $total_coupons,
                    'total_spent' => $total_spent,
                    'addresses' => $user->addresses,
                    'subscription' => $user->subscriptions->first(),
                    'wallet_transactions' => $wallet_transactions,
                    'referred_by_user' => $referred_by_user,
                    'total_referrals_made' => $total_referrals_made,
                    'recent_appointments' => $appointments->take(5)
                ]
            ], 200);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'view');
            return response()->json(['error' => 'User not found or error loading profile'], 500);
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

                if ($request->filter_type) {
                    if ($request->filter_type == 'active') {
                        $users->where('status', 1);
                    } elseif ($request->filter_type == 'suspended') {
                        $users->where('status', 0);
                    } elseif ($request->filter_type == 'app') {
                        $users->where('role', 1);
                    } elseif ($request->filter_type == 'web') {
                        $users->where('role', 2);
                    }
                }

                return DataTables::of($users)
                    ->addColumn('total_appointments', function ($row) {
                        return \App\Models\Appointment::where(function($q) use ($row) {
                            $q->where('user_id', $row->id);
                            if (!empty($row->mobile_number)) {
                                $q->orWhere('phone', $row->mobile_number);
                            }
                            if (!empty($row->email)) {
                                $q->orWhere('email', $row->email);
                            }
                        })->count();
                    })
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
                                'view_id' => $row->id,
                                'delete_id' => $row->id,
                                'current_status' => $row->status,
                                'hidden_id' => $row->id,
                            ]
                        ])->render();
                    })
                    ->editColumn('created_at', function($row) {
                        return Carbon::parse($row->created_at)->timezone('Asia/Kolkata')->format('d-m-Y H:i');
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
