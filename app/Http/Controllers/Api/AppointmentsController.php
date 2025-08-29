<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Exception;

class AppointmentsController extends Controller
{
    protected int $success_status;
    protected int $exception_status;
    protected int $backend_error_status;
    protected int $validation_error_status;
    protected string $common_error_message;
    protected string $controller_name;

    public function __construct()
    {
        $this->controller_name = 'API/AppointmentsController';
        $this->success_status = config('custom.status_code_for_success', 200);
        $this->exception_status = config('custom.status_code_for_exception_error', 500);
        $this->backend_error_status = config('custom.status_code_for_backend_error', 500);
        $this->validation_error_status = config('custom.status_code_for_validation_error', 422);
        $this->common_error_message = config('custom.common_error_message', 'Something went wrong.');
    }

    /**
     * Store a new appointment
     */
    public function bookAppointment(Request $request): JsonResponse
    {
        $function_name = 'bookAppointment';

        try {
            $validator = Validator::make($request->all(), [
                'service_category_id' => 'nullable|integer',
                'service_id'          => 'nullable|integer',
                'first_name'          => 'required|string|max:50',
                'last_name'           => 'nullable|string|max:50',
                'email'               => 'nullable|email|max:100',
                'phone'               => 'nullable|string|max:20',
                'quantity'            => 'nullable|integer|min:1',
                'price'               => 'nullable|numeric',
                'discount_price'      => 'nullable|numeric',
                'service_address'     => 'nullable|string',
                'appointment_date'    => 'nullable|date',
                'appointment_time'    => 'nullable',
                'special_notes'       => 'nullable|string',
                'status'              => 'nullable|in:0,1',
            ]);

            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            $appointment = Appointment::create([
                'order_number'        => '#BEAUTYDEN' . Str::upper(Str::random(8)),
                'first_name'          => $request->first_name,
                'last_name'           => $request->last_name,
                'email'               => $request->email,
                'phone'               => $request->phone,
                'service_id'          => $request->service_id,
                'service_category_id' => $request->service_category_id,
                'quantity'            => $request->quantity,
                'price'               => $request->price,
                'discount_price'      => $request->discount_price,
                'service_address'     => $request->service_address,
                'appointment_date'    => $request->appointment_date,
                'appointment_time'    => $request->appointment_time,
                'notes'               => $request->notes,
                'status'              => '0',
            ]);

            return $this->sendResponse(
                $appointment,
                'Appointment booked successfully.',
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
