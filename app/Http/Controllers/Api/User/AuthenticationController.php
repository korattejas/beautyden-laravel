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
use Exception;

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

    public function registrations(Request $request): JsonResponse
    {
        $function_name = 'registrations';
        try {
            $mobile_number = $request->mobile_number;
            $email = $request->email;


            if ($mobile_number) {
                $checkMobile = User::where('mobile_number', $mobile_number)->whereNull('mobile_verified_at')->first();
                if ($checkMobile) {
                    User::where('mobile_number', $mobile_number)->delete();
                    $checkMobile->delete();
                }
            } else {
                $checkEmail = User::where('email', $email)->whereNull('email_verified_at')->first();
                if ($checkEmail) {
                    User::where('email', $email)->delete();
                    $checkEmail->delete();
                }
            }

            $mobileExists = User::where('mobile_number', $mobile_number)->whereNull('mobile_verified_at')->exists();
            $emailExists = User::where('email', $email)->whereNull('email_verified_at')->exists();

            $validateArray = [
                'name' => 'required',
                'mobile_number' => [
                    'required_without:email',
                    'regex:/^\+?[1-9]\d{1,14}$/',
                    $mobileExists ? '' : 'unique:users,mobile_number',
                ],
                'email' => [
                    'required_without:mobile_number',
                    'email',
                    new ValidateEmail(),
                    $emailExists ? '' : 'unique:users,email',
                ],
                'password' => [
                    'required',
                    'min:8',
                    'regex:~^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[@$!%*#?&:,_./\s)(])[A-Za-z\d@$!%*#?&:,_./\s)(]{8,}$~',
                ],
                'confirm_password' => 'required|same:password',
            ];

            $validateMessage = [
                'name.required' => 'Name is required.',
                'mobile_number.required_without' => 'Mobile number is required if no email is provided.',
                'mobile_number.regex' => 'Enter a valid international mobile number with country code.',
                'mobile_number.unique' => 'Mobile number already exists!',
                'email.required_without' => 'Email is required if no mobile number is provided.',
                'email.email' => 'Enter a valid email address.',
                'email.unique' => 'Email already exists!',
                'password.required' => 'Password is required.',
                'password.regex' => 'Enter a password with at least 8 characters, one uppercase letter, one lowercase letter, one number, and one special character.',
                'password.min' => 'Password must be at least 8 characters long.',
                'confirm_password.required' => 'Confirm password is required.',
                'confirm_password.same' => 'Passwords do not match.',
            ];

            $validator = Validator::make($request->all(), $validateArray, $validateMessage);

            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            $insertData = [
                'name' => $request->name,
                'email' => $email,
                'mobile_number' => $mobile_number,
                'ip_address' => $request->ip(),
                'role' => 1,
                'password' => bcrypt($request->password),
            ];

            if ($mobile_number) {
                $mobileOtp = generateOTP($function_name);
                $insertData['otp'] = generateOTP($function_name);
                $otpExpirationTime = (int) config('custom.otp_expiration_time');
                $insertData['otp_expiration_at'] = now()->addSeconds($otpExpirationTime);


                $insertUser = User::create($insertData);
                // $this->sendMobileOtp(config('custom.whatsapp_otp_template'), $insertUser->mobile_number, $insertUser->name, $mobileOtp);

                $success = [
                    'user' => $insertData,
                    'mobile_number' => $mobile_number,
                    'otp_expiration_time' => config('custom.otp_expiration_time'),
                    'resend_otp_time' => config('custom.resend_otp_time'),
                    'resend_otp_max_limit' => config('custom.resend_otp_max_limit'),
                ];
            } else {
                $emailOtp = generateOTP($function_name);
                $insertData['otp'] = generateOTP($function_name);
                $otpExpirationTime = (int) config('custom.otp_expiration_time');
                $insertData['otp_expiration_at'] = now()->addSeconds($otpExpirationTime);

                $insertUser = User::create($insertData);
                // $this->sendEmailOtp($email, 'mail.candidate_update_profile_email_otp', config('custom.email_otp_title'), config('custom.email_otp_subject'), $request->name, $emailOtp);

                $success = [
                    'user' => $insertData,
                    'email' => $email,
                    'email_otp' => $emailOtp,
                    'otp_expiration_time' => config('custom.otp_expiration_time'),
                    'resend_otp_time' => config('custom.resend_otp_time'),
                    'resend_otp_max_limit' => config('custom.resend_otp_max_limit'),
                ];
            }

            return $this->sendResponse($success, 'User register successfully.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    public function verifyMobileOtpRegister(Request $request): JsonResponse
    {
        $function_name = 'verifyMobileOtpRegister';
        try {
            $validator = Validator::make($request->all(), [
                'otp' => 'required|numeric|digits:6',
                'mobile_number' => 'required|numeric|exists:users,mobile_number',
                'password' => [
                    'required',
                    'min:8',
                ],
            ], [
                'otp.required' => 'OTP is required.',
                'otp.numeric' => 'OTP number must be numeric.',
                'mobile_number.required' => 'Mobile number is required.',
                'mobile_number.numeric' => 'Mobile number must be numeric.',
                'password.required' => 'Password is required.',
                'password.min' => 'Password must be at least 8 characters long.',
            ]);

            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            $mobile_otp = $request->otp;
            $mobile_number = $request->mobile_number;
            $password = $request->password;

            $mobileOtpRecordCheck = User::where('mobile_number', $mobile_number)
                ->select('id', 'otp_expiration_at', 'otp', 'mobile_verified_at', 'email', 'mobile_number', 'password','name')
                ->first();

            if (!$mobileOtpRecordCheck) {
                logError($this->controller_name, $function_name, 'OTP store not properly.');
                return $this->sendError('Something went wrong.', $this->backend_error_status);
            }

            if ($mobileOtpRecordCheck->otp_expiration_at < now()) {
                return $this->sendError('Mobile OTP Expired.', $this->backend_error_status);
            }

            if ("$mobileOtpRecordCheck->otp" !== $mobile_otp) {
                return $this->sendError('Mobile OTP Invalid.', $this->backend_error_status);
            }

            if (is_null($mobileOtpRecordCheck->mobile_verified_at)) {

                $mobileOtpRecordCheck->update(['mobile_verified_at' => now()]);

                $token = auth()->guard('user')->attempt([
                    'mobile_number' => $mobile_number,
                    'password' => $password,
                    'status' => "1",
                ]);

                if (!$token) {
                    return $this->sendError('Invalid credentials.', $this->backend_error_status);
                }

                $success = [
                    'id' => $mobileOtpRecordCheck->id,
                    'name' => $mobileOtpRecordCheck->name,
                    'mobile_no' => $mobile_number,
                    'token' => $token,
                    'otp_expiration_time' => config('custom.otp_expiration_time'),
                    'resend_otp_time' => config('custom.resend_otp_time'),
                    'resend_otp_max_limit' => config('custom.resend_otp_max_limit'),
                ];

                return $this->sendResponse($success, 'Mobile number verified successfully.', $this->success_status);
            } else {
                return $this->sendError('Your mobile number is already verified.', $this->backend_error_status);
            }
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
