<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Twilio\Rest\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
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
                'service_sub_category_id' => 'nullable|integer',
                'service_id'          => 'required',
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

            $orderNumber = '#BEAUTYDEN' . Str::upper(Str::random(8));

            $appointment = Appointment::create([
                'order_number'        => $orderNumber,
                'first_name'          => $request->first_name,
                'last_name'           => $request->last_name,
                'email'               => $request->email,
                'phone'               => $request->phone,
                'service_id'          => $request->service_id,
                'service_category_id' => $request->service_category_id,
                'service_sub_category_id' => $request->service_sub_category_id,
                'quantity'            => $request->quantity,
                'price'               => $request->price,
                'discount_price'      => $request->discount_price,
                'service_address'     => $request->service_address,
                'appointment_date'    => $request->appointment_date,
                'appointment_time'    => $request->appointment_time,
                'notes'               => $request->notes,
                'status'              => '1',
            ]);

            // if (!empty($request->phone)) {
            //     $this->sendWhatsAppBooking($request->phone, $request->first_name, $orderNumber);
            // }

            $message = '<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
                            <p>Thank you for booking with <strong>BeautyDen</strong>! 💖</p>

                            <p><strong>📋 Your Order Number:</strong> <span style="color:#d63384;">' . $orderNumber . '</span></p>

                            <p>Your appointment request has been received successfully.</p>

                            <p>⏳ Our team will shortly review your booking details and check:</p>
                            <ul>
                                <li>Service availability</li>
                                <li>Provider schedule</li>
                                <li>Your location &amp; timing</li>
                            </ul>

                            <p>📌 Once everything is verified, we’ll confirm your appointment and share the final details with you.</p>

                            <p>✨ Sit back &amp; relax — you’re in safe hands with <strong>BeautyDen</strong>!</p>

                            <p>📞 If you don’t hear back from us soon, please feel free to reach us at:</p>
                            <ul>
                                <li><strong>WhatsApp:</strong> +91 95747 58282</li>
                                <li><strong>Email:</strong> contact@beautyden.com</li>
                                <li><strong>Phone:</strong> +91 95747 58282</li>
                            </ul>
                        </div>
                    ';

            return $this->sendResponse(
                [
                    'appointment'  => $appointment,
                    'order_number' => $orderNumber,
                ],
                $message,
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

    protected function sendWhatsAppBooking($phone, $orderDate, $appointmentTime)
    {
        try {
            $sid    = env('TWILIO_ACCOUNT_SID');
            $token  = env('TWILIO_AUTH_TOKEN');
            $from   = env('TWILIO_WHATSAPP_FROM'); // Twilio sandbox / business WhatsApp number

            $client = new Client($sid, $token);

            // Ensure number formatting
            $to = 'whatsapp:+91' . preg_replace('/\D/', '', $phone);

            // Example message template SID from your Twilio console
            $contentSid = "HXb5b62575e6e4ff6129ad7c8efe1f983e";

            // Variables to replace in your template
            $contentVariables = json_encode([
                "1" => $orderDate,
                "2" => $appointmentTime
            ]);

            $message = $client->messages->create($to, [
                "from" => $from,
                "contentSid" => $contentSid,
                "contentVariables" => $contentVariables,
                "body" => "Your appointment booking details"
            ]);

            Log::info("WhatsApp message sent, SID: " . $message->sid);
        } catch (\Exception $e) {
            Log::error("WhatsApp send failed: " . $e->getMessage());
        }
    }
}
