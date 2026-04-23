<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\CouponCode;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class CouponCodeController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/CouponCodeController";
    }

    public function index()
    {
        return view('admin.coupon_codes.index');
    }

    public function create()
    {
        return view('admin.coupon_codes.create');
    }

    public function edit($id)
    {
        try {
            $coupon = CouponCode::where('id', decryptId($id))->first();
            if ($coupon) {
                return view('admin.coupon_codes.edit', [
                    'coupon' => $coupon
                ]);
            }
            return redirect()->route('admin.coupon-codes.index')->with('error', 'Coupon not found');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'edit');
            return redirect()->route('admin.coupon-codes.index')->with('error', $this->error_message);
        }
    }

    public function getDataCouponCodes(Request $request)
    {
        try {
            if ($request->ajax()) {
                $coupons = CouponCode::query();
                return DataTables::of($coupons)
                    ->addColumn('discount', function ($coupon) {
                        return $coupon->discount_type == 'percentage' ? $coupon->discount_value . '%' : '₹' . $coupon->discount_value;
                    })
                    ->addColumn('validity', function ($coupon) {
                        $start = $coupon->start_date ? $coupon->start_date->format('d-m-Y') : 'N/A';
                        $end = $coupon->end_date ? $coupon->end_date->format('d-m-Y') : 'N/A';
                        return $start . ' to ' . $end;
                    })
                    ->addColumn('status', function ($coupon) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => $coupon->status
                        ];
                        return view('admin.render-view.datable-label', [
                            'status_array' => $status_array
                        ])->render();
                    })
                    ->addColumn('action', function ($coupon) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'edit_route' => route('admin.coupon-codes.edit', encryptId($coupon->id)),
                            'delete_id' => $coupon->id,
                            'current_status' => $coupon->status,
                            'hidden_id' => $coupon->id,
                        ];
                        return view('admin.render-view.datable-action', [
                            'action_array' => $action_array
                        ])->render();
                    })
                    ->rawColumns(['action', 'status'])
                    ->make(true);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'getDataCouponCodes');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function store(Request $request)
    {
        $function_name = 'store';
        $request_all = $request->all();
        try {
            $id = $request->input('edit_value');
            $validateArray = [
                'code' => [
                    'required',
                    $id == 0 ? 'unique:coupon_codes,code' : 'unique:coupon_codes,code,' . $id . ',id',
                ],
                'discount_type' => 'required|in:percentage,fixed',
                'discount_value' => 'required|numeric',
                'min_purchase_amount' => 'nullable|numeric',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ];

            $validator = Validator::make($request_all, $validateArray);
            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
            }

            $data = [
                'code' => strtoupper($request->code),
                'discount_type' => $request->discount_type,
                'discount_value' => $request->discount_value,
                'min_purchase_amount' => $request->min_purchase_amount ?? 0,
                'max_discount_amount' => $request->max_discount_amount,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'usage_limit' => $request->usage_limit,
                'usage_per_user' => $request->usage_per_user ?? 1,
                'is_first_order_only' => $request->has('is_first_order_only') ? 1 : 0,
                'description' => $request->description,
                'status' => (int) $request->status,
            ];

            if ($id == 0) {
                CouponCode::create($data);
                return response()->json([
                    'success' => true,
                    'message' => 'Coupon added successfully'
                ]);
            } else {
                CouponCode::where('id', $id)->update($data);
                return response()->json([
                    'success' => true,
                    'message' => 'Coupon updated successfully'
                ]);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function changeStatus($id, $status)
    {
        try {
            CouponCode::where('id', $id)->update(['status' => $status]);
            return response()->json(['message' => trans('admin_string.msg_status_change')]);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'changeStatus');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function destroy(int $id)
    {
        try {
            $coupon = CouponCode::find($id);
            if ($coupon) {
                $coupon->delete();
                return response()->json(['message' => 'Coupon deleted successfully']);
            }
            return response()->json(['error' => 'Coupon not found.'], 500);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'destroy');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }
}
