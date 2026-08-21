<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Exception;

class PaymentTypeController extends Controller
{
    protected mixed $success_status, $exception_status, $backend_error_status, $validation_error_status, $common_error_message;
    protected string $controller_name;

    public function __construct()
    {
        $this->controller_name = 'API/PaymentTypeController';
        $this->success_status = config('custom.status_code_for_success');
        $this->exception_status = config('custom.status_code_for_exception_error');
        $this->backend_error_status = config('custom.status_code_for_backend_error');
        $this->validation_error_status = config('custom.status_code_for_validation_error');
        $this->common_error_message = config('custom.common_error_message');
    }

    public function getPaymentTypes(): JsonResponse
    {
        $function_name = 'getPaymentTypes';
        try {
            $paymentTypes = DB::table('payment_types')
                ->select(
                    'id',
                    'name',
                    DB::raw('CONCAT("' . asset('uploads/payment-type') . '/", icon) AS icon'),
                    'status',
                    'created_at',
                    'updated_at'
                )
                ->where('status', 1)
                ->get()
                ->map(function ($type) {
                    $type->status = (int) $type->status;
                    return $type;
                });

            if ($paymentTypes->isEmpty()) {
                return $this->sendError('No payment types found.', $this->backend_error_status);
            }

            return $this->sendResponse(
                $paymentTypes,
                'Payment types retrieved successfully',
                $this->success_status
            );
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError(
                $this->common_error_message,
                $this->exception_status
            );
        }
    }
}
