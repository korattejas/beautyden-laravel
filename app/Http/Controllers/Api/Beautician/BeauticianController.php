<?php

namespace App\Http\Controllers\Api\Beautician;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Exception;
use Carbon\Carbon;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Service;
use Twilio\Rest\Client as TwilioClient;

class BeauticianController extends Controller
{
    protected mixed $success_status, $exception_status, $backend_error_status, $validation_error_status, $common_error_message;
    protected string $controller_name;

    public function __construct()
    {
        $this->controller_name = 'API/Beautician/BeauticianController';
        $this->success_status = config('custom.status_code_for_success', 200);
        $this->exception_status = config('custom.status_code_for_exception_error', 500);
        $this->backend_error_status = config('custom.status_code_for_backend_error', 500);
        $this->validation_error_status = config('custom.status_code_for_validation_error', 422);
        $this->common_error_message = config('custom.common_error_message', 'Something went wrong.');
    }

    /**
     * Send OTP for Beautician Login
     */
    public function sendLoginOtp(Request $request): JsonResponse
    {
        $function_name = 'sendLoginOtp';
        try {
            $validator = Validator::make($request->all(), [
                'mobile_number' => 'required|numeric|regex:/^\+?[1-9]\d{1,14}$/'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            $mobile_number = $request->mobile_number;
            
            // Check if this mobile exists in TeamMember table
            $teamMember = TeamMember::where('phone', $mobile_number)
                ->orWhere('phone', 'like', '%' . substr($mobile_number, -10))
                ->first();

            if (!$teamMember) {
                return $this->sendError('This mobile number is not registered as a Beautician.', 404);
            }

            $otp = rand(100000, 999999);
            $expiry = now()->addMinutes(10);

            $user = User::where('mobile_number', $mobile_number)->first();
            if ($user) {
                $user->update([
                    'otp' => $otp,
                    'otp_expiration_at' => $expiry,
                    'role' => 3, // Ensure role is beautician
                ]);
            } else {
                $user = User::create([
                    'name' => $teamMember->name,
                    'mobile_number' => $mobile_number,
                    'otp' => $otp,
                    'otp_expiration_at' => $expiry,
                    'role' => 3, // Beautician role
                    'status' => 1,
                ]);
            }

            // Send OTP via WhatsApp using Helper
            $this->sendWhatsAppOtp($mobile_number, $teamMember->name, $otp);

            $data = [
                'mobile_number' => $mobile_number,
                'message' => 'OTP sent successfully via WhatsApp.'
            ];

            return $this->sendResponse($data, 'OTP sent to your mobile number.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    /**
     * Send OTP via WhatsApp via Twilio (Same as AppointmentsController)
     */
    protected function sendWhatsAppOtp($phone, $name, $otp)
    {
        try {
            $authKey = env('MSG91_AUTH_KEY');
            $senderNumber = env('MSG91_WHATSAPP_NUMBER');
            $templateName = 'beautyden_otp';

            $cleanedNumber = preg_replace('/\D/', '', $phone);
            // Ensure number has country code for MSG91
            if (strlen($cleanedNumber) == 10) {
                $to = '91' . $cleanedNumber;
            } else {
                $to = $cleanedNumber;
            }

            $response = Http::withHeaders([
                'authkey' => $authKey,
                'Content-Type' => 'application/json'
            ])->post('https://api.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/bulk/', [
                'integrated_number' => $senderNumber,
                'content_type' => 'template',
                'payload' => [
                    'messaging_product' => 'whatsapp',
                    'type' => 'template',
                    'template' => [
                        'name' => $templateName,
                        'language' => [
                            'code' => 'en',
                            'policy' => 'deterministic'
                        ],
                        'namespace' => '74620ab4_9b20_468c_8d6d_d17ebaa631a0',
                        'to_and_components' => [
                            [
                                'to' => [(string) $to, '916352755075'],
                                'components' => [
                                    'body_1' => [
                                        'type' => 'text',
                                        'value' => (string) $otp
                                    ],
                                    'button_1' => [
                                        'subtype' => 'url',
                                        'type' => 'text',
                                        'value' => (string) $otp
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                Log::info("MSG91 WhatsApp OTP sent successfully to $to. Response: " . $response->body());
            } else {
                Log::error("MSG91 WhatsApp OTP send failed for $to. Status: " . $response->status() . " Body: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("MSG91 WhatsApp OTP send exception: " . $e->getMessage());
        }
    }

    /**
     * Verify OTP and Login Beautician
     */
    public function verifyLoginOtp(Request $request): JsonResponse
    {
        $function_name = 'verifyLoginOtp';
        try {
            $validator = Validator::make($request->all(), [
                'mobile_number' => 'required|numeric',
                'otp' => 'required|numeric'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            $user = User::where('mobile_number', $request->mobile_number)->first();

            if (!$user || $user->otp != $request->otp) {
                return $this->sendError('Invalid OTP.', 401);
            }

            if ($user->otp_expiration_at < now()) {
                return $this->sendError('OTP expired.', 401);
            }

            // Verify mobile and clear OTP
            $user->update([
                'mobile_verified_at' => now(),
                'otp' => null,
                'otp_expiration_at' => null
            ]);

            $token = JWTAuth::fromUser($user);

            $data = [
                'token' => $token,
                'beautician' => $user,
            ];

            return $this->sendResponse($data, 'Login successful.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    /**
     * Get the TeamMember ID for the authenticated user
     */
    private function getTeamMember($request)
    {
        $user = auth()->guard('user')->user();
        if ($user) {
            $phone = preg_replace('/\D/', '', $user->mobile_number);
            return TeamMember::whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, '+', ''), '-', ''), ' ', ''), '(', '') LIKE '%$phone%'")
                ->orWhere('phone', $user->mobile_number)
                ->first();
        }

        return null;
    }

    /**
     * Beautician Dashboard Stats
     */
    public function dashboard(Request $request): JsonResponse
    {
        $function_name = 'dashboard';
        try {
            $teamMember = $this->getTeamMember($request);
            if (!$teamMember) {
                return $this->sendError('Beautician profile not found.', 404);
            }

            $query = Appointment::whereRaw("FIND_IN_SET(?, assigned_to)", [$teamMember->id]);

            $totalCompleted = (clone $query)->where('status', 3)->count();
            $monthlyCompleted = (clone $query)->where('status', 3)
                ->whereMonth('appointment_date', Carbon::now()->month)
                ->whereYear('appointment_date', Carbon::now()->year)
                ->count();
            $pendingAppointments = (clone $query)->whereIn('status', [1, 2])->count();
            $totalAppointments = (clone $query)->count();
            $todaysAppointments = (clone $query)->whereDate('appointment_date', Carbon::today())->count();

            $data = [
                'beautician_name' => $teamMember->name,
                'total_completed' => $totalCompleted,
                'monthly_completed' => $monthlyCompleted,
                'pending_appointments' => $pendingAppointments,
                'total_appointments' => $totalAppointments,
                'todays_appointments' => $todaysAppointments,
            ];

            return $this->sendResponse($data, 'Dashboard data fetched successfully.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    /**
     * List of Appointments for Beautician
     */
    public function getAppointments(Request $request): JsonResponse
    {
        $function_name = 'getAppointments';
        try {
            $teamMember = $this->getTeamMember($request);
            if (!$teamMember) {
                return $this->sendError('Beautician profile not found.', 404);
            }

            $query = Appointment::whereRaw("FIND_IN_SET(?, assigned_to)", [$teamMember->id])
                ->leftJoin('cities as ct', 'ct.id', '=', 'appointments.city_id')
                ->select('appointments.*', 'ct.name as city_name')
                ->orderBy('appointment_date', 'desc')
                ->orderBy('appointment_time', 'desc');

            // Date filter
            if ($request->filled('date')) {
                $query->whereDate('appointment_date', $request->date);
            }

            // Status filter
            if ($request->filled('status')) {
                $query->where('appointments.status', $request->status);
            }

            // Month/Year filter
            if ($request->filled('month') && $request->month != 'all') {
                $query->whereMonth('appointment_date', $request->month);
            }
            if ($request->filled('year') && $request->year != 'all') {
                $query->whereYear('appointment_date', $request->year);
            }

            $appointments = $query->get();

            $formattedAppointments = $appointments->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'order_number' => $appointment->order_number,
                    'client_name' => $appointment->first_name . ' ' . $appointment->last_name,
                    'phone' => $appointment->phone,
                    'appointment_date' => $appointment->appointment_date,
                    'appointment_time' => $appointment->appointment_time,
                    'address' => $appointment->service_address,
                    'city' => $appointment->city_name,
                    'status' => $appointment->status,
                    'total_amount' => $appointment->price,
                ];
            });

            return $this->sendResponse($formattedAppointments, 'Appointments fetched successfully.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    /**
     * Appointment Details
     */
    public function getAppointmentDetails(Request $request): JsonResponse
    {
        $function_name = 'getAppointmentDetails';
        try {
            $validator = Validator::make($request->all(), [
                'appointment_id' => 'required|integer|exists:appointments,id',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            $teamMember = $this->getTeamMember($request);
            if (!$teamMember) {
                return $this->sendError('Beautician profile not found.', 404);
            }

            $appointment = Appointment::whereRaw("FIND_IN_SET(?, assigned_to)", [$teamMember->id])
                ->leftJoin('cities as ct', 'ct.id', '=', 'appointments.city_id')
                ->select('appointments.*', 'ct.name as city_name')
                ->where('appointments.id', $request->appointment_id)
                ->first();

            if (!$appointment) {
                return $this->sendError('Appointment not found or not assigned to you.', 404);
            }

            // Extract service names if available
            $services = [];
            if (isset($appointment->services_data['services'])) {
                $services = $appointment->services_data['services'];
            }

            $data = [
                'id' => $appointment->id,
                'order_number' => $appointment->order_number,
                'client_details' => [
                    'name' => $appointment->first_name . ' ' . $appointment->last_name,
                    'phone' => $appointment->phone,
                    'email' => $appointment->email,
                ],
                'appointment_details' => [
                    'date' => $appointment->appointment_date,
                    'time' => $appointment->appointment_time,
                    'address' => $appointment->service_address,
                    'city' => $appointment->city_name,
                    'notes' => $appointment->special_notes,
                ],
                'services' => $services,
                'summary' => $appointment->services_data['summary'] ?? null,
                'status' => $appointment->status,
            ];

            return $this->sendResponse($data, 'Appointment details fetched successfully.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    /**
     * Update Appointment Status
     */
    public function updateStatus(Request $request): JsonResponse
    {
        $function_name = 'updateStatus';
        try {
            $validator = Validator::make($request->all(), [
                'appointment_id' => 'required|integer|exists:appointments,id',
                'status' => 'required|in:1,2,3,4', // 1=Pending, 2=Assigned, 3=Completed, 4=Rejected/Cancelled
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            $teamMember = $this->getTeamMember($request);
            if (!$teamMember) {
                return $this->sendError('Beautician profile not found.', 404);
            }

            $appointment = Appointment::whereRaw("FIND_IN_SET(?, assigned_to)", [$teamMember->id])
                ->where('id', $request->appointment_id)
                ->first();

            if (!$appointment) {
                return $this->sendError('Appointment not assigned to you.', 403);
            }

            $appointment->status = $request->status;
            $appointment->save();

            return $this->sendResponse($appointment, 'Appointment status updated successfully.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    /**
     * Get Beautician Profile
     */
    public function getProfile(Request $request): JsonResponse
    {
        $function_name = 'getProfile';
        try {
            $teamMember = $this->getTeamMember($request);
            if (!$teamMember) {
                return $this->sendError('Beautician profile not found.', 404);
            }

            $data = [
                'id' => $teamMember->id,
                'name' => $teamMember->name,
                'phone' => $teamMember->phone,
                'role' => $teamMember->role,
                'experience' => $teamMember->experience_years,
                'bio' => $teamMember->bio,
                'address' => $teamMember->address,
                'photo' => $teamMember->icon ? asset('uploads/team-member/' . $teamMember->icon) : null,
                'specialties' => $teamMember->specialties ? json_decode($teamMember->specialties, true) : [],
                'certifications' => $teamMember->certifications ? json_decode($teamMember->certifications, true) : [],
            ];

            return $this->sendResponse($data, 'Profile fetched successfully.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }
}
