<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\ValidateEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use Twilio\Rest\Client;
use Exception;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\ServiceCategory;
use App\Models\ServiceSubcategory;
use App\Models\City;

class AuthenticationController extends Controller
{
    protected mixed $success_status, $exception_status, $backend_error_status, $validation_error_status, $pin_code_validation_error_status, $common_error_message;

    protected string $controller_name;

    public function __construct()
    {
        $this->controller_name = 'API/AuthenticationController';
        $this->success_status = config('custom.status_code_for_success');
        $this->exception_status = config('custom.status_code_for_exception_error');
        $this->backend_error_status = config('custom.status_code_for_backend_error');
        $this->validation_error_status = config('custom.status_code_for_validation_error');
        $this->pin_code_validation_error_status = config('custom.status_code_for_pin_code_validation_error');
        $this->common_error_message = config('custom.common_error_message');
    }

    public function sendOtpOnMobileNumber(Request $request): JsonResponse
    {
        $function_name = 'sendOtpOnMobileNumber';
        try {
            $mobile_number = $request->mobile_number;

            $validateArray = [
                'mobile_number' => [
                    'required',
                    'regex:/^\+?[1-9]\d{1,14}$/'
                ]
            ];

            $validateMessage = [
                'mobile_number.required' => 'Mobile number is required.',
                'mobile_number.regex' => 'Enter a valid international mobile number with country code.',
                'mobile_number.unique' => 'Mobile number already exists!',
            ];

            $validator = Validator::make($request->all(), $validateArray, $validateMessage);

            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            $otp = rand(100000, 999999);
            $otpExpirationTime = (int) config('custom.otp_expiration_time');
            $expiry = now()->addSeconds($otpExpirationTime);

            $user = User::where('mobile_number', $mobile_number)->first();

            if ($user) {
                $user->update([
                    'otp' => $otp,
                    'otp_expiration_at' => $expiry,
                    'ip_address' => $request->ip(),
                    'mobile_verified_at' => null,
                ]);
            } else {
                $user = User::create([
                    'mobile_number' => $mobile_number,
                    'otp' => $otp,
                    'otp_expiration_at' => $expiry,
                    'ip_address' => $request->ip(),
                    'role' => 2,
                ]);
            }

            // $this->sendWhatsAppOtp($mobile_number, $otp);
            $user = User::where('mobile_number', $mobile_number)->first();
            $success = [
                'customer' => $user,
                'otp_expiration_time' => $otpExpirationTime,
                'resend_otp_time' => config('custom.resend_otp_time'),
                'resend_otp_max_limit' => config('custom.resend_otp_max_limit'),
            ];


            return $this->sendResponse($success, 'User register successfully.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    protected function sendWhatsAppOtp($phone, $otp)
    {
        try {
            $sid    = env('TWILIO_ACCOUNT_SID');
            $token  = env('TWILIO_AUTH_TOKEN');
            $from   = env('TWILIO_WHATSAPP_FROM');
            $templateSid = 'HX3e6d0aac83cf42ed0c9269971562d295';

            $client = new Client($sid, $token);
            $cleanedNumber = preg_replace('/\D/', '', $phone);
            $to = 'whatsapp:+91' . $cleanedNumber;

            $contentVariables = json_encode(['1' => (string) $otp], JSON_UNESCAPED_UNICODE);

            $message = $client->messages->create($to, [
                'from' => $from,
                'contentSid' => $templateSid,
                'contentVariables' => $contentVariables
            ]);

            logger()->error("✅ WhatsApp OTP sent successfully. SID: " . $message->sid);
        } catch (\Exception $e) {
            logger()->error("❌ WhatsApp OTP send failed: " . $e->getMessage());
        }
    }

    public function verifyOtpOnMobileNumber(Request $request): JsonResponse
    {
        $function_name = 'verifyOtpOnMobileNumber';
        try {
            $validator = Validator::make($request->all(), [
                'otp' => 'required|numeric|digits:6',
                'mobile_number' => 'required|numeric|exists:users,mobile_number',
            ]);

            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            $mobile_number = preg_replace('/\D/', '', $request->mobile_number);
            $otp = $request->otp;

            $user = User::where('mobile_number', $mobile_number)->first();

            if (!$user) {
                return $this->sendError('User not found.', $this->backend_error_status);
            }

            if ($user->otp_expiration_at < now()) {
                return $this->sendError('OTP expired.', $this->backend_error_status);
            }

            if ($user->otp != $otp) {
                return $this->sendError('Invalid OTP.', $this->backend_error_status);
            }

            if (is_null($user->mobile_verified_at)) {
                $user->update(['mobile_verified_at' => now()]);

                $token = JWTAuth::fromUser($user);

                $success = [
                    'id' => $user->id,
                    'mobile_no' => $mobile_number,
                    'token' => $token,
                ];

                return $this->sendResponse($success, 'Mobile verified successfully.', $this->success_status);
            } else {
                return $this->sendError('Mobile number already verified.', $this->backend_error_status);
            }
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    public function profileUpdate(Request $request): JsonResponse
    {
        $function_name = 'profileUpdate';

        try {
            $validateArray = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'address' => 'required|string|max:500',
                'mobile_number' => [
                    'required',
                    'regex:/^\+?[1-9]\d{1,14}$/',
                ],
            ];

            $validateMessage = [
                'mobile_number.required' => 'Mobile number is required.',
                'mobile_number.regex' => 'Enter a valid international mobile number with country code.',
                'email.email' => 'Enter a valid email address.',
            ];

            $validator = Validator::make($request->all(), $validateArray, $validateMessage);

            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            $authUser = User::where('mobile_number', $request->mobile_number)
                ->whereNotNull('mobile_verified_at')
                ->first();

            if (!$authUser) {
                return $this->sendError('User not found or not verified.', 404);
            }

            if ($request->filled('email') && $request->email !== $authUser->email) {
                $emailExists = User::where('email', $request->email)
                    ->where('id', '!=', $authUser->id)
                    ->exists();

                if ($emailExists) {
                    return $this->sendError('This email is already in use by another account.', 409);
                }

                $authUser->email = $request->email;
            }

            $authUser->name = $request->name ?? $authUser->name;
            $authUser->address = $request->address ?? $authUser->address;
            $authUser->save();

            $success = [
                'customer' => $authUser->fresh(),
            ];

            return $this->sendResponse($success, 'Profile updated successfully.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }


    public function getProfile(Request $request): JsonResponse
    {
        $function_name = 'getProfile';

        try {
            $mobile_number = $request->mobile_number;

            $validator = Validator::make($request->all(), [
                'mobile_number' => [
                    'required',
                    'regex:/^\+?[1-9]\d{1,14}$/',
                ],
            ], [
                'mobile_number.required' => 'Mobile number is required.',
                'mobile_number.regex' => 'Enter a valid international mobile number with country code.',
            ]);

            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            $authUser = User::where('mobile_number', $mobile_number)
                ->whereNotNull('mobile_verified_at')
                ->first();

            if (!$authUser) {
                return $this->sendError('User not found or not verified.', 404);
            }

            $success = [
                'customer' => [
                    'name' => $authUser->name,
                    'email' => $authUser->email,
                    'address' => $authUser->address,
                    'mobile_number' => $authUser->mobile_number,
                    'mobile_verified_at' => $authUser->mobile_verified_at,
                ],
            ];

            return $this->sendResponse($success, 'Profile fetched successfully.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    public function getTotalBookService(Request $request): JsonResponse
    {
        $function_name = 'getTotalBookService';

        try {
            $validator = Validator::make($request->all(), [
                'mobile_number' => [
                    'required',
                    'regex:/^\+?[1-9]\d{1,14}$/',
                ],
            ], [
                'mobile_number.required' => 'Mobile number is required.',
                'mobile_number.regex' => 'Enter a valid international mobile number with country code.',
            ]);

            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            $mobile_number = $request->mobile_number;

            $authUser = User::where('mobile_number', $mobile_number)
                ->whereNotNull('mobile_verified_at')
                ->first();

            if (!$authUser) {
                return $this->sendError('User not found or not verified.', 404);
            }

            $appointments = Appointment::leftJoin('cities as ct', 'ct.id', '=', 'appointments.city_id')
                ->select(
                    'appointments.id',
                    'appointments.order_number',
                    'appointments.appointment_date',
                    'appointments.appointment_time',
                    'appointments.service_id',
                    'ct.name as city_name'
                )
                ->where('appointments.phone', $mobile_number)
                ->orderByDesc('appointments.id')
                ->get();

            if ($appointments->isEmpty()) {
                return $this->sendError('No bookings found for this user.', 404);
            }

            $data = $appointments->map(function ($appointment) {
                $serviceIds = $appointment->service_id ? explode(',', $appointment->service_id) : [];
                $totalServices = count(array_filter($serviceIds));

                return [
                    'id'                => $appointment->id,
                    'order_number'      => $appointment->order_number,
                    'appointment_date'  => $appointment->appointment_date,
                    'appointment_time'  => $appointment->appointment_time,
                    'city_name'         => $appointment->city_name,
                    'total_services'    => $totalServices,
                ];
            });

            return $this->sendResponse($data, 'All booking details fetched successfully.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }


    public function getBookServiceDetails(Request $request): JsonResponse
    {
        $function_name = 'getBookServiceDetails';

        try {
            $validator = Validator::make($request->all(), [
                'mobile_number' => [
                    'required',
                    'regex:/^\+?[1-9]\d{1,14}$/',
                ],
                'appointment_id' => 'required|integer',
            ], [
                'mobile_number.required' => 'Mobile number is required.',
                'mobile_number.regex' => 'Enter a valid international mobile number with country code.',
                'appointment_id.required' => 'Appointment ID is required.',
            ]);

            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            $mobile_number = $request->mobile_number;
            $appointmentId = $request->appointment_id;

            $authUser = User::where('mobile_number', $mobile_number)
                ->whereNotNull('mobile_verified_at')
                ->first();

            if (!$authUser) {
                return $this->sendError('User not found or not verified.', 404);
            }

            $appointment = Appointment::leftJoin('service_categories as sc', 'sc.id', '=', 'appointments.service_category_id')
                ->leftJoin('service_subcategories as ssc', 'ssc.id', '=', 'appointments.service_sub_category_id')
                ->leftJoin('cities as ct', 'ct.id', '=', 'appointments.city_id')
                ->select(
                    'appointments.*',
                    'sc.name as service_category_name',
                    'ssc.name as service_sub_category_name',
                    'ct.name as city_name',
                )
                ->where('appointments.id', $appointmentId)
                ->first();

            if (!$appointment) {
                return $this->sendError('Appointment not found.', 404);
            }

            $serviceIds = $appointment->service_id ? explode(',', $appointment->service_id) : [];
            $serviceIds = array_map('intval', $serviceIds);
            $services = Service::whereIn('id', $serviceIds)->pluck('name')->toArray();

            $data = [
                'id'                     => $appointment->id,
                'order_number'           => $appointment->order_number,
                'price'                  => $appointment->price,
                'discount_price'         => $appointment->discount_price,
                'service_address'        => $appointment->service_address,
                'appointment_date'       => $appointment->appointment_date,
                'appointment_time'       => $appointment->appointment_time,
                'special_notes'          => $appointment->special_notes,
                'status'                 => $appointment->status,
                'created_at'             => $appointment->created_at,
                'updated_at'             => $appointment->updated_at,
                'city_name'              => $appointment->city_name,
                'services'               => $services,
            ];

            return $this->sendResponse($data, 'Booking details fetched successfully.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    public function sendResendMobileOrForgotPasswordOtp(Request $request): JsonResponse
    {
        $function_name = 'sendMobileOrEmailOrForgotPasswordOtp';
        try {
            $is_forgot = $request->is_forgot;

            $userIdentifier = $request->mobile_number;

            $sessionKey = ($is_forgot == 1 ? 'forget' : 'register') . '_otp_resend_mobile_' . $userIdentifier;

            $resendCount = session($sessionKey, 0);
            if ($resendCount >= config('custom.resend_otp_max_limit')) {
                return $this->sendError('You have exceeded the resend limit.', $this->validation_error_status);
            }

            $validator = Validator::make($request->all(), [
                'mobile_number' => [
                    'required',
                    'regex:/^\+?[1-9]\d{1,14}$/',
                    $is_forgot == 1 ? '' : 'exists:users,mobile_number',
                ],
            ], [
                'mobile_number.required' => 'Mobile number is required.',
                'mobile_number.numeric' => 'Mobile number must be numeric.',
            ]);

            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            session([$sessionKey => $resendCount + 1]);

            return $this->processOtp('mobile_number', $request->mobile_number, $is_forgot);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    public function login(Request $request): JsonResponse
    {
        $function_name = 'login';
        try {
            $validateArray = [
                'mobile_number' => 'required|numeric|regex:/^\+?[1-9]\d{1,14}$/',
                'password' => 'required',
            ];
            $validateMessage = [
                'mobile_number.required' => 'Mobile number is required.',
                'mobile_number.numeric' => 'Mobile number must be in numeric.',
                'mobile_number.digits' => 'Mobile number must be 10 number.',
                'mobile_number.unique' => 'Mobile number already exists!',
                'password.required' => 'Password is required.',
                'password.min' => 'Password must be at least 8 characters long.',
                'password.regex' => 'Password must be with an uppercase letter, a lowercase letter, a number and a special character.',
            ];

            $validator = Validator::make($request->all(), $validateArray, $validateMessage);
            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            $checkUser = User::where('mobile_number', $request->mobile_number)->first();
            if (!$checkUser) {
                return $this->sendError('Please first complete your register.', $this->backend_error_status);
            }
            if ($checkUser->mobile_verified_at == null) {
                return $this->sendError('Mobile number not verified', $this->backend_error_status);
            }
            if ($checkUser->status == 0) {
                return $this->sendError('Your profile is currently deactivated. Please contact the administrator.', $this->backend_error_status);
            }
            if (!Hash::check($request->password, $checkUser->password)) {
                return $this->sendError('Your password does not match with our records.', $this->backend_error_status);
            }

            if ($token = auth()->guard('user')->attempt(['mobile_number' => $request->mobile_number, 'password' => $request->password, 'status' => 1])) {
                $user = User::select(
                    'users.id',
                    'users.name',
                    'users.mobile_number',
                )->first();

                $success = [
                    'user' => $user,
                    'token' => $token,
                ];
                return $this->sendResponse($success, 'User Login successfully.', $this->success_status);
            } else {
                return $this->sendError('Your mobile number & password does not match with our records.', $this->backend_error_status);
            }
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    public function logout(): JsonResponse
    {
        $function_name = 'logout';
        try {
            $token = JWTAuth::getToken();
            JWTAuth::invalidate($token);
            auth()->guard('user')->logout();
            return response()->json(['status' => 200, 'message' => 'Logged out successfully']);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    private function processOtp($key, $value, $is_forgot): JsonResponse
    {
        $verification = User::where($key, $value)->first();
        if (!$verification) {
            $errorKey = ($key == 'email') ? 'Email' : 'Mobile number';
            return $this->sendError("$errorKey not exists.", $this->backend_error_status);
        }
        $otpType = ($key == 'email') ? 'email' : 'mobile';
        $newOtp = generateOTP('processOtp');
        $otpExpirationTime = (int) config('custom.otp_expiration_time');
        $expiration = now()->addSeconds($otpExpirationTime);
        $verification->update([
            'otp' => $newOtp,
            'otp_expiration_at' => $expiration,
        ]);

        $user = User::where($key, $value)->select('name')->first();

        if ($user) {
            $templateView = ($is_forgot == 1) ? 'candidate_forgot_password_email_otp' : 'candidate_update_profile_email_otp';


            if ($otpType == 'mobile') {
                $templateId = ($is_forgot == 1) ? config('custom.forgot_password_template') : config('custom.whatsapp_otp_template');
                // $this->sendMobileOtp($templateId, $value, $user->name, $newOtp);
            } else {
                // $this->sendEmailOtp($value, 'mail.' . $templateView, config('custom.email_otp_title'), config('constants.reset_password'), $user->name, $newOtp);
            }
        }

        $success = [
            $otpType . '_otp' => $newOtp,
            $key => $verification->$key,
            'otp_expiration_time' => config('custom.otp_expiration_time'),
            'resend_otp_time' => config('custom.resend_otp_time'),
            'resend_otp_max_limit' => config('custom.resend_otp_max_limit'),
        ];

        return $this->sendResponse($success, 'OTP sent successfully', $this->success_status);
    }

    public function verifyEmailOtpRegister(Request $request): JsonResponse
    {
        $function_name = 'verifyEmailOtpRegister';
        $request_all = $request->all();
        try {
            $validator = Validator::make($request_all, [
                'otp' => 'required|numeric|digits:6',
                'email' => 'required|email|exists:users,email',
            ], [
                'otp.required' => 'Email OTP is required.',
                'otp.numeric' => 'Email OTP number must be numeric.',
                'email.required' => 'Email is required.',
                'email.email' => 'Enter a valid email.',
            ]);

            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            $emailOtp = $request->otp;
            $email_id = $request->mobile_no;

            $emailOtpRecordCheck = User::where('email', $email_id)
                ->select('id', 'otp_expiration_at', 'otp', 'email_verified_at', 'email_id', 'mobile_no')
                ->first();

            if (!$emailOtpRecordCheck) {
                logError($this->controller_name, $function_name, 'OTP store not properly.');
                return $this->sendError('Something went wrong.', $this->backend_error_status);
            }

            if ($emailOtpRecordCheck->otp_expiration_at < now()) {
                return $this->sendError('Email OTP Expired.', $this->backend_error_status);
            }

            if ("$emailOtpRecordCheck->otp" !== $emailOtp) {
                return $this->sendError('Email OTP Invalid.', $this->backend_error_status);
            }

            if (is_null($emailOtpRecordCheck->email_verified_at)) {
                $emailOtpRecordCheck->update(['email_verified_at' => now()]);
                $token = auth()->guard('web')->attempt(credentials: ['email' => $emailOtpRecordCheck->email, 'password' => $emailOtpRecordCheck->password, 'status' => 1]);


                $success = [
                    'email' => $emailOtpRecordCheck->email,
                    'token' => $token,
                    'otp_expiration_time' => config('custom.otp_expiration_time'),
                    'resend_otp_time' => config('custom.resend_otp_time'),
                    'resend_otp_max_limit' => config('custom.resend_otp_max_limit'),
                ];

                return $this->sendResponse($success, 'Email id verified successfully.', $this->success_status);
            } else {
                return $this->sendError('Your email id is already verified.', $this->backend_error_status);
            }
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    private function sendMobileOtp($whatsAppTemplate, $mobile_no, $name, $otp): void
    {
        $params = json_encode([$name, $otp]);
        sendWhatsAppOtp($mobile_no, $whatsAppTemplate, $params);
    }

    private function sendEmailOtp($email, $view, $tittle, $subject, $name, $otp): void
    {
        $email_data = [
            'to' => $email,
            'view' => $view,
            'title' => $tittle,
            'subject' => $subject,
            'name' => $name,
            'otp' => $otp,
        ];

        sendEmail($email_data);
    }
}
