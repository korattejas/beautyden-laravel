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
                            $wa_url = "https://wa.me/91" . $usage->user->mobile_number;
                            return '<div class="d-flex flex-column">
                                        <span class="fw-bold text-dark">' . $usage->user->name . '</span>
                                        <a href="'.$wa_url.'" target="_blank" class="text-muted small d-flex align-items-center">
                                            <i class="bi bi-whatsapp text-success me-25"></i>' . $usage->user->mobile_number . '
                                        </a>
                                    </div>';
                        }
                        return '<span class="badge bg-light-secondary">Guest</span>';
                    })
                    ->addColumn('appointment_number', function ($usage) {
                        $appointment = \App\Models\Appointment::find($usage->appointment_id);
                        if ($appointment) {
                            $url = route('admin.appointments.edit', encryptId($appointment->id));
                            return '<a href="'.$url.'" class="fw-bold text-primary d-flex align-items-center">
                                        <i class="bi bi-hash me-25"></i>'.$appointment->order_number.'
                                    </a>';
                        }
                        return '<span class="text-muted">N/A</span>';
                    })
                    ->addColumn('discount', function ($usage) {
                        return '<span class="fw-bold text-success" style="font-family: \'JetBrains Mono\', monospace;">₹' . number_format($usage->discount_amount, 2) . '</span>';
                    })
                    ->addColumn('used_at', function ($usage) {
                        return '<div class="d-flex flex-column" style="line-height: 1.2;">
                                    <span class="text-dark fw-bold">' . $usage->created_at->format('d-m-Y') . '</span>
                                    <span class="text-muted small">' . $usage->created_at->format('h:i A') . '</span>
                                </div>';
                    })
                    ->addColumn('action', function ($usage) {
                        return '<div class="d-flex justify-content-center">
                                    <button class="btn btn-icon btn-flat-danger delete-record" data-id="'.$usage->id.'" title="Delete Usage">
                                        <i class="bi bi-trash-fill" style="font-size: 1.2rem;"></i>
                                    </button>
                                </div>';
                    })
                    ->rawColumns(['action', 'user_details', 'appointment_number', 'discount', 'used_at'])
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
