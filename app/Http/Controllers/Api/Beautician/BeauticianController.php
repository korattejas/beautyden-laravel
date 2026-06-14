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
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\BeauticianSettlement;

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

            $teamMember = TeamMember::where('phone', $mobile_number)
                ->orWhere('phone', 'like', '%' . substr($mobile_number, -10))
                ->first();

            if (!$teamMember && !$request->has('name')) {
                return $this->sendResponse(['is_exists' => false], 'Beautician not found. Please register.', $this->success_status);
            }

            $otp = rand(100000, 999999);
            $otpExpirationTime = (int) config('custom.otp_expiration_time');
            $expiry = now()->addSeconds($otpExpirationTime);

            if (!$teamMember) {
                $regValidator = Validator::make($request->all(), [
                    'name' => 'required|string|max:255',
                    'dob' => 'required|date',
                    'bio' => 'required|string',
                    'experience_years' => 'required|numeric',
                    'address' => 'required|string',
                ]);

                if ($regValidator->fails()) {
                    return $this->sendError($regValidator->errors()->first(), $this->validation_error_status);
                }

                $teamMember = TeamMember::create([
                    'name' => $request->name,
                    'phone' => $mobile_number,
                    'dob' => $request->dob,
                    'bio' => $request->bio,
                    'experience_years' => $request->experience_years,
                    'address' => $request->address,
                    'status' => 0,
                ]);

                $user = User::updateOrCreate(
                    ['mobile_number' => $mobile_number],
                    [
                        'name' => $request->name,
                        'dob' => $request->dob,
                        'address' => $request->address,
                        'role' => 2,
                        'status' => 1,
                        'otp' => $otp,
                        'otp_expiration_at' => $expiry,
                    ]
                );
            } else {
                $user = User::where('mobile_number', $mobile_number)->first();
                if ($user) {
                    $user->update([
                        'otp' => $otp,
                        'otp_expiration_at' => $expiry,
                        'role' => 2,
                    ]);
                } else {
                    $user = User::create([
                        'name' => $teamMember->name,
                        'mobile_number' => $mobile_number,
                        'dob' => $teamMember->dob,
                        'address' => $teamMember->address,
                        'otp' => $otp,
                        'otp_expiration_at' => $expiry,
                        'role' => 2,
                        'status' => 1,
                    ]);
                }
            }

            // Send OTP via WhatsApp using Helper
            $this->sendWhatsAppOtp($mobile_number, $teamMember->name, $otp);

            $data = [
                'is_exists' => true,
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

            // Check if beautician is approved (status 1)
            if ($teamMember->status != 1) {
                $data = [
                    'beautician_name' => $teamMember->name,
                    'is_approved' => false,
                    'message' => 'Your profile is currently under review. Your dashboard and appointment details will be visible once the administrator approves your account.'
                ];
                return $this->sendResponse($data, 'Profile under review.', $this->success_status);
            }

            $baseQuery = Appointment::whereRaw("FIND_IN_SET(?, assigned_to)", [$teamMember->id]);
            $query = clone $baseQuery;

            // Filtering logic
            $filter = $request->get('filter', 'last_30_days');
            $startDate = null;
            $endDate = Carbon::now();

            switch ($filter) {
                case 'today':
                    $startDate = Carbon::today();
                    $endDate = Carbon::today();
                    break;
                case 'yesterday':
                    $startDate = Carbon::yesterday();
                    $endDate = Carbon::yesterday();
                    break;
                case 'last_7_days':
                    $startDate = Carbon::now()->subDays(7);
                    break;
                case 'last_30_days':
                    $startDate = Carbon::now()->subDays(30);
                    break;
                case 'last_3_months':
                    $startDate = Carbon::now()->subMonths(3);
                    break;
                case 'last_6_months':
                    $startDate = Carbon::now()->subMonths(6);
                    break;
                case 'last_1_year':
                    $startDate = Carbon::now()->subYear();
                    break;
                case 'all':
                    $startDate = null;
                    break;
                case 'custom':
                    if ($request->filled('start_date') && $request->filled('end_date')) {
                        $startDate = Carbon::parse($request->start_date);
                        $endDate = Carbon::parse($request->end_date);
                    }
                    break;
                default:
                    $startDate = Carbon::now()->subDays(30);
                    $filter = 'last_30_days';
                    break;
            }

            if ($startDate) {
                $query->whereBetween('appointment_date', [$startDate->toDateString(), $endDate->toDateString()]);
            }

            $totalCompleted = (clone $query)->where('status', 3)->count();
            
            $completedAppointments = (clone $query)->where('status', 3)->get();
            $totalRevenue = $completedAppointments->sum(function($appointment) {
                return (float) ($appointment->services_data['summary']['grand_total'] ?? 0);
            });

            $pendingAppointments = (clone $query)->whereIn('status', [1, 2])->count();
            $totalAppointments = (clone $query)->count();

            $todayAppointmentsCount = (clone $baseQuery)->whereDate('appointment_date', Carbon::today())->count();
            $tomorrowAppointmentsCount = (clone $baseQuery)->whereDate('appointment_date', Carbon::tomorrow())->count();

            $repeatPhones = (clone $baseQuery)
                ->select('phone')
                ->groupBy('phone')
                ->havingRaw('COUNT(id) > 1')
                ->pluck('phone');

            $repeatCustomersCount = $repeatPhones->count();

            $repeatCustomersList = (clone $baseQuery)
                ->whereIn('appointments.phone', $repeatPhones)
                ->leftJoin('cities as ct', 'ct.id', '=', 'appointments.city_id')
                ->select('appointments.*', 'ct.name as city_name')
                ->orderBy('appointment_date', 'desc')
                ->get()
                ->map(function ($appointment) {
                    $services = [];
                    if (isset($appointment->services_data['services'])) {
                        $services = $appointment->services_data['services'];
                    }
                    return [
                        'id' => $appointment->id,
                        'order_number' => $appointment->order_number,
                        'client_details' => [
                            'name' => $appointment->first_name . ' ' . $appointment->last_name,
                            'phone' => $appointment->phone,
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
                        'payment_type' => $appointment->payment_type,
                    ];
                });

            $data = [
                'beautician_name' => $teamMember->name,
                'is_approved' => true,
                'filter_applied' => $filter,
                'total_completed' => $totalCompleted,
                'total_revenue' => round($totalRevenue, 2),
                'pending_appointments' => $pendingAppointments,
                'total_appointments' => $totalAppointments,
                'today_appointments_count' => $todayAppointmentsCount,
                'tomorrow_appointments_count' => $tomorrowAppointmentsCount,
                'repeat_customers_count' => $repeatCustomersCount,
                'repeat_customers_list' => $repeatCustomersList,
                'start_date' => $startDate ? $startDate->toDateString() : null,
                'end_date' => $endDate->toDateString(),
                'settlement' => BeauticianSettlement::where('team_member_id', $teamMember->id)->first(),
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

            if ($teamMember->status != 1) {
                return $this->sendError('Your profile is under review. Appointment details will be available once approved.', 403);
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
                    'appointment_date' => $appointment->appointment_date,
                    'appointment_time' => $appointment->appointment_time,
                    'address' => $appointment->service_address,
                    'city' => $appointment->city_name,
                    'status' => $appointment->status,
                    'total_amount' => $appointment->services_data['summary']['grand_total'] ?? $appointment->price,
                    'company_amount' => $appointment->company_amount,
                    'payment_type' => $appointment->payment_type,
                ];
            });

            return $this->sendResponse($formattedAppointments, 'Appointments fetched successfully.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    /**
     * Export Appointments (Excel/PDF)
     */
    public function exportAppointments(Request $request)
    {
        $function_name = 'exportAppointments';
        try {
            $teamMember = $this->getTeamMember($request);
            if (!$teamMember) {
                return $this->sendError('Beautician profile not found.', 404);
            }

            if ($teamMember->status != 1) {
                return $this->sendError('Your profile is under review.', 403);
            }

            $query = Appointment::whereRaw("FIND_IN_SET(?, assigned_to)", [$teamMember->id])
                ->leftJoin('cities as ct', 'ct.id', '=', 'appointments.city_id')
                ->select('appointments.*', 'ct.name as city_name')
                ->orderBy('appointment_date', 'desc');

            // Apply filters
            if ($request->filled('date')) {
                $query->whereDate('appointment_date', $request->date);
            }
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('appointment_date', [$request->start_date, $request->end_date]);
            }
            if ($request->filled('status')) {
                $query->where('appointments.status', $request->status);
            }
            if ($request->filled('month') && $request->month != 'all') {
                $query->whereMonth('appointment_date', $request->month);
            }
            if ($request->filled('year') && $request->year != 'all') {
                $query->whereYear('appointment_date', $request->year);
            }

            $appointments = $query->get();
            $type = $request->get('type', 'excel'); // excel or pdf
            // Use team member ID to overwrite the file and save server space
            $fileName = 'appointments_report_beautician_' . $teamMember->id . ($type == 'pdf' ? '.pdf' : '.xlsx');
            $directory = public_path('uploads/exports');

            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }

            if ($type == 'pdf') {
                $pdf = Pdf::loadView('admin.appointments.export', compact('appointments'));
                $pdf->save($directory . '/' . $fileName);
            } else {
                config(['filesystems.disks.public_exports' => [
                    'driver' => 'local',
                    'root' => $directory,
                ]]);
                
                $exportClass = new class($appointments) implements \Maatwebsite\Excel\Concerns\FromView {
                    private $appointments;
                    public function __construct($appointments) { $this->appointments = $appointments; }
                    public function view(): \Illuminate\Contracts\View\View {
                        return view('admin.appointments.export', ['appointments' => $this->appointments]);
                    }
                };

                Excel::store($exportClass, $fileName, 'public_exports');
            }

            // Append time as query param to bypass cache, but keep the same file on server
            $fileUrl = asset('uploads/exports/' . $fileName) . '?v=' . time();

            return $this->sendResponse([
                'file_url' => $fileUrl
            ], 'File generated successfully.', $this->success_status);
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

            if ($teamMember->status != 1) {
                return $this->sendError('Your profile is under review. Appointment details will be available once approved.', 403);
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

            // Fetch associated review
            $review = \App\Models\CustomerReview::where('appointment_id', $appointment->id)->first();
            if ($review) {
                $photos = is_array($review->photos) ? $review->photos : [];
                $review->photos = array_map(function($photo) {
                    return asset('uploads/review/photos/' . $photo);
                }, $photos);
            }

            $data = [
                'id' => $appointment->id,
                'order_number' => $appointment->order_number,
                'client_details' => [
                    'name' => $appointment->first_name . ' ' . $appointment->last_name,
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
                'company_amount' => $appointment->company_amount,
                'status' => $appointment->status,
                'payment_type' => $appointment->payment_type,
                'review' => $review,
            ];

            return $this->sendResponse($data, 'Appointment details fetched successfully.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    /**
     * Get Repeat Customer Appointments
     */
    public function getRepeatCustomers(Request $request): JsonResponse
    {
        $function_name = 'getRepeatCustomers';
        try {
            $teamMember = $this->getTeamMember($request);
            if (!$teamMember) {
                return $this->sendError('Beautician profile not found.', 404);
            }

            if ($teamMember->status != 1) {
                return $this->sendError('Your profile is under review.', 403);
            }

            // Identify phones that have more than 1 appointment assigned to this beautician
            $repeatPhones = Appointment::whereRaw("FIND_IN_SET(?, assigned_to)", [$teamMember->id])
                ->select('phone')
                ->groupBy('phone')
                ->havingRaw('COUNT(id) > 1')
                ->pluck('phone');

            // Fetch all appointments associated with these repeat phone numbers
            $appointments = Appointment::whereRaw("FIND_IN_SET(?, assigned_to)", [$teamMember->id])
                ->whereIn('phone', $repeatPhones)
                ->leftJoin('cities as ct', 'ct.id', '=', 'appointments.city_id')
                ->select('appointments.*', 'ct.name as city_name')
                ->orderBy('appointment_date', 'desc')
                ->get();

            $data = $appointments->map(function ($appointment) {
                $services = [];
                if (isset($appointment->services_data['services'])) {
                    $services = $appointment->services_data['services'];
                }

                return [
                    'id' => $appointment->id,
                    'order_number' => $appointment->order_number,
                    'client_details' => [
                        'name' => $appointment->first_name . ' ' . $appointment->last_name,
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
                    'payment_type' => $appointment->payment_type,
                ];
            });

            return $this->sendResponse($data, 'Repeat customer appointments fetched successfully.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    /**
     * Update Appointment Status
     */
    public function appointmentUpdateStatus(Request $request): JsonResponse
    {
        $function_name = 'appointmentUpdateStatus';
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
     * Update Beautician Profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $function_name = 'updateProfile';
        try {
            $teamMember = $this->getTeamMember($request);
            if (!$teamMember) {
                return $this->sendError('Beautician profile not found.', 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string|max:255',
                'dob' => 'nullable|date',
                'blood_group' => 'nullable|string|max:10',
                'role' => 'nullable|string|max:255',
                'experience_years' => 'nullable|numeric',
                'specialties' => 'nullable|string',
                'bio' => 'nullable|string',
                'certifications' => 'nullable|string',
                'state' => 'nullable|string',
                'city' => 'nullable|string',
                'taluko' => 'nullable|string',
                'village' => 'nullable|string',
                'address' => 'nullable|string',
                'icon' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            $updateData = $request->only([
                'name', 'dob', 'blood_group', 'role', 'experience_years',
                'specialties', 'bio', 'certifications', 'state', 'city',
                'taluko', 'village', 'address'
            ]);

            if ($request->hasFile('icon')) {
                // Delete old icon if exists
                if ($teamMember->icon && file_exists(public_path('uploads/team-member/' . $teamMember->icon))) {
                    @unlink(public_path('uploads/team-member/' . $teamMember->icon));
                }

                $image = $request->file('icon');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/team-member/'), $imageName);
                $updateData['icon'] = $imageName;
            }

            $teamMember->update($updateData);

            // Also update User table name if provided
            if ($request->filled('name')) {
                $user = auth()->guard('user')->user();
                if ($user) {
                    $user->update(['name' => $request->name]);
                }
            }

            return $this->sendResponse($teamMember, 'Profile updated successfully.', $this->success_status);
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
                'dob' => $teamMember->dob,
                'blood_group' => $teamMember->blood_group,
                'role' => $teamMember->role,
                'experience_years' => $teamMember->experience_years,
                'bio' => $teamMember->bio,
                'state' => $teamMember->state,
                'city' => $teamMember->city,
                'taluko' => $teamMember->taluko,
                'village' => $teamMember->village,
                'address' => $teamMember->address,
                'photo' => $teamMember->icon ? asset('uploads/team-member/' . $teamMember->icon) : null,
                'specialties' => $teamMember->specialties,
                'certifications' => $teamMember->certifications,
                'status' => $teamMember->status,
            ];

            return $this->sendResponse($data, 'Profile fetched successfully.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    /**
     * Logout Beautician
     */
    public function logout(): JsonResponse
    {
        $function_name = 'logout';
        try {
            $token = JWTAuth::getToken();
            if ($token) {
                JWTAuth::invalidate($token);
            }
            auth()->guard('user')->logout();
            
            return $this->sendResponse([], 'Logged out successfully.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }
}
