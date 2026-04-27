<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;
use Carbon\Carbon;

class MembershipController extends Controller
{
    protected mixed $success_status, $exception_status, $backend_error_status, $validation_error_status, $common_error_message;
    protected string $controller_name;

    public function __construct()
    {
        $this->controller_name = 'API/MembershipController';
        $this->success_status = config('custom.status_code_for_success', 200);
        $this->exception_status = config('custom.status_code_for_exception_error', 500);
        $this->backend_error_status = config('custom.status_code_for_backend_error', 500);
        $this->validation_error_status = config('custom.status_code_for_validation_error', 422);
        $this->common_error_message = config('custom.common_error_message', 'Something went wrong.');
    }

    /**
     * List all available membership plans
     */
    public function listPlans(Request $request): JsonResponse
    {
        $function_name = 'listPlans';
        try {
            $plans = MembershipPlan::where('status', 1)->get();
            
            $authUser = auth('user')->user();
            $mySubscription = $authUser ? $authUser->activeSubscription() : null;

            $data = [
                'plans' => $plans,
                'my_subscription' => $mySubscription ? [
                    'plan_name' => $mySubscription->plan->name,
                    'expiry_date' => $mySubscription->end_date,
                    'discount_percentage' => $mySubscription->plan->discount_percentage
                ] : null
            ];

            return $this->sendResponse($data, 'Plans fetched successfully.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    /**
     * Purchase a membership
     * NOTE: In a real app, this would involve a payment gateway.
     * For now, we'll create the subscription directly.
     */
    public function buyMembership(Request $request): JsonResponse
    {
        $function_name = 'buyMembership';
        try {
            $authUser = auth('user')->user();
            if (!$authUser) {
                return $this->sendError('User not authenticated.', 401);
            }

            $plan = MembershipPlan::where('id', $request->plan_id)->where('status', 1)->first();
            if (!$plan) {
                return $this->sendError('Invalid or inactive plan selected.', 404);
            }

            // Deactivate any existing active subscriptions
            UserSubscription::where('user_id', $authUser->id)
                ->where('status', 1)
                ->update(['status' => 0]);

            $startDate = now();
            $endDate = (clone $startDate)->addMonths($plan->duration_months);

            $subscription = UserSubscription::create([
                'user_id' => $authUser->id,
                'plan_id' => $plan->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'price_paid' => $plan->price,
                'payment_id' => $request->payment_id ?? 'OFFLINE_' . time(),
                'status' => 1
            ]);

            return $this->sendResponse($subscription, 'Membership activated successfully!', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }
}
