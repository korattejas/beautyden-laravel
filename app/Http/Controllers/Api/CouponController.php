<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\CouponCode;
use App\Models\CouponUsage;
use App\Models\Appointment;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class CouponController extends Controller
{
    protected $success_status, $exception_status, $backend_error_status, $validation_error_status, $common_error_message;

    public function __construct()
    {
        $this->success_status = config('custom.status_code_for_success');
        $this->exception_status = config('custom.status_code_for_exception_error');
        $this->backend_error_status = config('custom.status_code_for_backend_error');
        $this->validation_error_status = config('custom.status_code_for_validation_error');
        $this->common_error_message = config('custom.common_error_message');
    }

    public function listCoupons(): JsonResponse
    {
        $function_name = 'listCoupons';
        try {
            $user = auth('user')->user();
            $now = Carbon::now()->toDateString();
            
            $coupons = CouponCode::where('status', 1)
                ->where(function($query) use ($now) {
                    $query->whereNull('start_date')->orWhere('start_date', '<=', $now);
                })
                ->where(function($query) use ($now) {
                    $query->whereNull('end_date')->orWhere('end_date', '>=', $now);
                })
                ->get();

            $validCoupons = [];
            foreach ($coupons as $coupon) {
                // Check usage limit
                if ($coupon->usage_limit !== null) {
                    $totalUsed = CouponUsage::where('coupon_id', $coupon->id)->count();
                    if ($totalUsed >= $coupon->usage_limit) continue;
                }

                // Check first order only
                if ($coupon->is_first_order_only) {
                    $hasPreviousOrder = Appointment::where('phone', $user->mobile_number)->exists();
                    if ($hasPreviousOrder) continue;
                }

                // Check per user limit
                $userUsage = CouponUsage::where('coupon_id', $coupon->id)
                    ->where('user_id', $user->id)
                    ->count();
                if ($userUsage >= $coupon->usage_per_user) continue;

                $validCoupons[] = [
                    'id' => $coupon->id,
                    'code' => $coupon->code,
                    'discount_type' => $coupon->discount_type,
                    'discount_value' => $coupon->discount_value,
                    'min_purchase_amount' => $coupon->min_purchase_amount,
                    'max_discount_amount' => $coupon->max_discount_amount,
                    'description' => $coupon->description,
                ];
            }

            return $this->sendResponse($validCoupons, 'Coupons retrieved successfully', $this->success_status);

        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    public function applyCoupon(Request $request): JsonResponse
    {
        $function_name = 'applyCoupon';
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string',
                'amount' => 'required|numeric'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            $user = auth('user')->user();
            $coupon = CouponCode::where('code', strtoupper($request->code))
                ->where('status', 1)
                ->first();

            if (!$coupon) {
                return $this->sendError('Invalid coupon code', $this->validation_error_status);
            }

            // Check Dates
            $now = Carbon::now();
            if ($coupon->start_date && $now->lt($coupon->start_date)) {
                return $this->sendError('Coupon is not yet active', $this->validation_error_status);
            }
            if ($coupon->end_date && $now->gt($coupon->end_date)) {
                return $this->sendError('Coupon has expired', $this->validation_error_status);
            }

            // Check Min Amount
            if ($request->amount < $coupon->min_purchase_amount) {
                return $this->sendError('Minimum amount required: ₹' . $coupon->min_purchase_amount, $this->validation_error_status);
            }

            // Check usage limit
            if ($coupon->usage_limit !== null) {
                $totalUsed = CouponUsage::where('coupon_id', $coupon->id)->count();
                if ($totalUsed >= $coupon->usage_limit) {
                    return $this->sendError('Coupon usage limit reached', $this->validation_error_status);
                }
            }

            // Check per user limit
            $userUsage = CouponUsage::where('coupon_id', $coupon->id)
                ->where('user_id', $user->id)
                ->count();
            if ($userUsage >= $coupon->usage_per_user) {
                return $this->sendError('You have already used this coupon maximum times', $this->validation_error_status);
            }

            // Check first order only
            if ($coupon->is_first_order_only) {
                $hasPreviousOrder = Appointment::where('phone', $user->mobile_number)->exists();
                if ($hasPreviousOrder) {
                    return $this->sendError('This coupon is for new users only', $this->validation_error_status);
                }
            }

            // Calculate Discount
            $discount = 0;
            if ($coupon->discount_type == 'percentage') {
                $discount = ($request->amount * $coupon->discount_value) / 100;
                if ($coupon->max_discount_amount && $discount > $coupon->max_discount_amount) {
                    $discount = $coupon->max_discount_amount;
                }
            } else {
                $discount = $coupon->discount_value;
            }

            $data = [
                'coupon_id' => $coupon->id,
                'code' => $coupon->code,
                'discount_amount' => round($discount, 2),
                'final_amount' => round($request->amount - $discount, 2)
            ];

            return $this->sendResponse($data, 'Coupon applied successfully', $this->success_status);

        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }
}
