<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CouponUsage;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CouponUsageController extends Controller
{
    protected $error_message, $exception_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->controller_name = "Admin/CouponUsageController";
    }

    public function index()
    {
        return view('admin.coupon_usages.index');
    }

    public function getDataCouponUsages(Request $request)
    {
        try {
            if ($request->ajax()) {
                $usages = CouponUsage::with(['coupon', 'user'])->select('coupon_usages.*');
                
                return DataTables::of($usages)
                    ->addColumn('coupon_code', function ($usage) {
                        return $usage->coupon ? $usage->coupon->code : 'N/A';
                    })
                    ->addColumn('user_details', function ($usage) {
                        if ($usage->user) {
                            return $usage->user->name . '<br><small>' . $usage->user->mobile_number . '</small>';
                        }
                        return 'Guest';
                    })
                    ->addColumn('appointment_number', function ($usage) {
                        $appointment = \App\Models\Appointment::find($usage->appointment_id);
                        return $appointment ? $appointment->order_number : 'N/A';
                    })
                    ->addColumn('discount', function ($usage) {
                        return '₹' . $usage->discount_amount;
                    })
                    ->addColumn('used_at', function ($usage) {
                        return $usage->created_at->format('d-m-Y H:i');
                    })
                    ->addColumn('action', function ($usage) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'delete_id' => $usage->id,
                            'hidden_id' => $usage->id,
                        ];
                        // Using a simplified layout for delete only
                        return '<a href="javascript:void(0)" class="text-danger delete-record" data-id="'.$usage->id.'">
                                    <i data-feather="trash-2"></i> Delete Usage
                                </a>';
                    })
                    ->rawColumns(['action', 'user_details'])
                    ->make(true);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'getDataCouponUsages');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function destroy($id)
    {
        try {
            $usage = CouponUsage::find($id);
            if ($usage) {
                $usage->delete();
                return response()->json(['message' => 'Usage record deleted. User can now reuse the coupon.']);
            }
            return response()->json(['error' => 'Record not found.'], 500);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'destroy');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }
}
