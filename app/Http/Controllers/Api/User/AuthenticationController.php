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
use App\Models\UserFcmToken;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

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

    public function updateFcmToken(Request $request): JsonResponse
    {
        $function_name = 'updateFcmToken';
        try {
            $authUser = auth('user')->user();

            if (!$authUser) {
                return $this->sendError('User not authenticated.', 401);
            }

            $validator = Validator::make($request->all(), [
                'fcm_token' => 'required|string',
                'device_type' => 'nullable|string|in:android,ios',
                'device_name' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            $token = UserFcmToken::where('user_id', $authUser->id)
                ->where('fcm_token', $request->fcm_token)
                ->first();

            if ($token) {
                $token->update([
                    'device_type' => $request->device_type ?? $token->device_type,
                    'device_name' => $request->device_name ?? $token->device_name,
                    'ip_address' => $request->ip(),
                ]);
            } else {
                UserFcmToken::create([
                    'user_id' => $authUser->id,
                    'fcm_token' => $request->fcm_token,
                    'device_type' => $request->device_type,
                    'device_name' => $request->device_name,
                    'ip_address' => $request->ip(),
                ]);
            }

            return $this->sendResponse([], 'FCM token updated successfully.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
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
            ];

            $validator = Validator::make($request->all(), $validateArray, $validateMessage);

            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            // --- CUSTOM OTP PROGRESSIVE DELAY LOGIC START ---
            // IP based check (Max 20 OTP requests per IP per 15 minutes)
            $ipAddress = $request->ip();
            $cacheKeyIpAttempts = 'otp_ip_attempts_' . $ipAddress;

            $ipAttempts = \Illuminate\Support\Facades\Cache::get($cacheKeyIpAttempts, 0);
            if ($ipAttempts >= 20) {
                return $this->sendError("Too many requests from your network. Please try again after 15 minutes.", $this->validation_error_status);
            }

            $cacheKeyAttempts = 'otp_attempts_' . date('Y-m-d') . '_' . $mobile_number;
            $cacheKeyBlock = 'otp_block_' . $mobile_number;

            if (\Illuminate\Support\Facades\Cache::has($cacheKeyBlock)) {
                $unblockTime = \Illuminate\Support\Facades\Cache::get($cacheKeyBlock);
                $secondsLeft = $unblockTime - time();
                
                if ($secondsLeft > 0) {
                    if ($secondsLeft < 60) {
                        $timeMsg = $secondsLeft . ' seconds';
                    } elseif ($secondsLeft < 3600) {
                        $timeMsg = ceil($secondsLeft / 60) . ' minutes';
                    } else {
                        $timeMsg = ceil($secondsLeft / 3600) . ' hours';
                    }
                    return $this->sendError("Too many OTP requests. Please wait $timeMsg.", $this->validation_error_status);
                }
            }

            // Increment IP attempts ONLY when the mobile block check is passed
            $ipAttempts++;
            \Illuminate\Support\Facades\Cache::put($cacheKeyIpAttempts, $ipAttempts, now()->addMinutes(15));

            $attempts = \Illuminate\Support\Facades\Cache::get($cacheKeyAttempts, 0);
            $attempts++;
            \Illuminate\Support\Facades\Cache::put($cacheKeyAttempts, $attempts, now()->endOfDay());

            if ($attempts == 3) {
                \Illuminate\Support\Facades\Cache::put($cacheKeyBlock, time() + 120, 120); // 2 minutes
            } elseif ($attempts == 4) {
                \Illuminate\Support\Facades\Cache::put($cacheKeyBlock, time() + 900, 900); // 15 minutes
            } elseif ($attempts == 5) {
                \Illuminate\Support\Facades\Cache::put($cacheKeyBlock, time() + 21600, 21600); // 6 hours
            } elseif ($attempts >= 6) {
                \Illuminate\Support\Facades\Cache::put($cacheKeyBlock, time() + 86400, 86400); // 24 hours
            }
            // --- CUSTOM OTP PROGRESSIVE DELAY LOGIC END ---

            $user = User::where('mobile_number', $mobile_number)->first();

            $otp = rand(100000, 999999);
            $otpExpirationTime = (int) config('custom.otp_expiration_time');
            $expiry = now()->addSeconds($otpExpirationTime);

            if ($user) {
                $user->update([
                    'otp' => $otp,
                    'otp_expiration_at' => $expiry,
                    'ip_address' => $request->ip(),
                    'mobile_verified_at' => null,
                ]);
            } else {
                $user = User::create([
                    'name' => 'Customer',
                    'mobile_number' => $mobile_number,
                    'otp' => $otp,
                    'otp_expiration_at' => $expiry,
                    'ip_address' => $request->ip(),
                    'role' => 1,
                    'status' => 1,
                ]);
            }

            $this->sendWhatsAppOtp($mobile_number, $user->name, $otp);

            $success = [
                'otp_expiration_time' => $otpExpirationTime,
                'resend_otp_time' => config('custom.resend_otp_time'),
                'resend_otp_max_limit' => config('custom.resend_otp_max_limit'),
                'is_new_user' => !$user->wasRecentlyCreated ? false : true,
            ];

            return $this->sendResponse($success, 'OTP sent successfully.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

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

    public function verifyOtpOnMobileNumber(Request $request): JsonResponse
    {
        $function_name = 'verifyOtpOnMobileNumber';
        try {
            $validator = Validator::make($request->all(), [
                'otp' => 'required|numeric|digits:6',
                'mobile_number' => 'required|numeric|exists:users,mobile_number',
                'referral_code' => 'nullable|string|exists:users,referral_code',
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

            $isFirstVerification = is_null($user->mobile_verified_at);
            
            $updateData = [
                'mobile_verified_at' => now(),
                'otp' => null,
                'otp_expiration_at' => null
            ];

            if ($isFirstVerification) {
                // Generate a unique 6-character alphanumeric referral code (e.g., 9FIO03)
                do {
                    $newReferralCode = strtoupper(\Illuminate\Support\Str::random(6));
                } while (\App\Models\User::where('referral_code', $newReferralCode)->exists());
                
                $updateData['referral_code'] = $newReferralCode;

                // Handle if they applied someone else's referral code
                if ($request->has('referral_code') && !empty($request->referral_code)) {
                    $referrer = \App\Models\User::where('referral_code', $request->referral_code)->first();
                    if ($referrer && $referrer->id != $user->id) {
                        $updateData['referred_by'] = $referrer->id;
                        
                        // Give signup bonus to the new user (Referee)
                        $refereeBonus = \App\Models\AppSetting::where('key', 'signup_bonus_amount')->value('value') ?? 10;
                        if ($refereeBonus > 0) {
                            $updateData['wallet_balance'] = $user->wallet_balance + $refereeBonus;
                            
                            $wt = \App\Models\WalletTransaction::create([
                                'user_id' => $user->id,
                                'type' => 'credit',
                                'amount' => $refereeBonus,
                                'description' => 'Signup Referral Bonus',
                                'reference_id' => $referrer->id
                            ]);

                            // Notify referee about the wallet addition
                            \App\Services\NotificationService::trigger($user->id, 'wallet_added', [
                                '{amount}' => $refereeBonus
                            ], $wt->id);
                        }
                    }
                }

                // Send Welcome Notification
                \App\Services\NotificationService::trigger($user->id, 'welcome');
            }

            $user->update($updateData);

            $token = JWTAuth::fromUser($user);

            $success = [
                'id' => $user->id,
                'name' => $user->name,
                'mobile_no' => $mobile_number,
                'token' => $token,
                'customer' => $user
            ];

            return $this->sendResponse($success, 'Mobile verified successfully.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    public function profileUpdate(Request $request): JsonResponse
    {
        $function_name = 'profileUpdate';

        try {
            $authUser = auth('user')->user();

            if (!$authUser) {
                return $this->sendError('User not authenticated.', 401);
            }

            $validateArray = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'dob' => 'nullable|date',
            ];

            $validateMessage = [
                'email.email' => 'Enter a valid email address.',
                'dob.date' => 'Enter a valid date of birth.',
            ];

            $validator = Validator::make($request->all(), $validateArray, $validateMessage);

            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
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
            $authUser->dob = $request->dob ?? $authUser->dob;
            if ($request->has('address')) {
                $authUser->address = $request->address;
            }
            $authUser->save();

            $success = [
                'customer' => $authUser->fresh()->load(['addresses' => function($query) {
                    $query->orderBy('is_default', 'desc')->orderBy('id', 'desc');
                }]),
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
            $authUser = auth('user')->user();

            if (!$authUser) {
                return $this->sendError('User not authenticated.', 401);
            }

            $success = [
                'customer' => [
                    'id' => $authUser->id,
                    'name' => $authUser->name,
                    'email' => $authUser->email,
                    'dob' => $authUser->dob,
                    'address' => $authUser->address,
                    'mobile_number' => $authUser->mobile_number,
                    'mobile_verified_at' => $authUser->mobile_verified_at,
                    'city_id' => $authUser->city_id,
                    'referral_code' => $authUser->referral_code,
                    'wallet_balance' => $authUser->wallet_balance,
                    'addresses' => $authUser->addresses()->orderBy('is_default', 'desc')->orderBy('id', 'desc')->get(),
                ],
            ];

            return $this->sendResponse($success, 'Profile fetched successfully.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    public function getWalletHistory(Request $request): JsonResponse
    {
        $function_name = 'getWalletHistory';

        try {
            $authUser = auth('user')->user();

            if (!$authUser) {
                return $this->sendError('User not authenticated.', 401);
            }

            $transactions = \App\Models\WalletTransaction::where('user_id', $authUser->id)
                ->orderBy('id', 'desc')
                ->get()
                ->map(function ($transaction) {
                    $referenceName = null;
                    if (str_contains($transaction->description, 'Referral Bonus') || str_contains($transaction->description, 'Signup Bonus')) {
                        $referenceUser = \App\Models\User::find($transaction->reference_id);
                        if ($referenceUser) {
                            $referenceName = $referenceUser->name;
                        }
                    } else if (str_contains($transaction->description, 'Booking')) {
                        $appointment = \App\Models\Appointment::find($transaction->reference_id);
                        if ($appointment) {
                            $referenceName = $appointment->order_number;
                        } else {
                            $referenceName = "Booking #" . $transaction->reference_id;
                        }
                    }

                    $data = $transaction->toArray();
                    $data['reference_name'] = $referenceName;
                    return $data;
                });

            $signupBonus = \App\Models\AppSetting::where('key', 'signup_bonus_amount')->value('value') ?? 50;
            $referrerBonus = \App\Models\AppSetting::where('key', 'referral_reward_amount')->value('value') ?? 50;
            $walletLimitPercent = \App\Models\AppSetting::where('key', 'wallet_usage_limit_percent')->value('value') ?? 20;

            $success = [
                'wallet_balance' => $authUser->wallet_balance,
                'referral_code' => $authUser->referral_code,
                'referral_settings' => [
                    'signup_bonus_amount' => $signupBonus,
                    'referral_reward_amount' => $referrerBonus,
                    'wallet_usage_limit_percent' => $walletLimitPercent,
                    'instructions' => [
                        "Share your unique referral code with your friends and family.",
                        "When your friend registers using your code, they get ₹{$signupBonus} in their wallet instantly.",
                        "You will receive ₹{$referrerBonus} in your wallet after your friend successfully completes their first service booking.",
                        "You can use your wallet balance to pay up to {$walletLimitPercent}% of your total booking amount."
                    ],
                    'faqs' => [
                        [
                            'question' => 'How do I invite my friends?',
                            'answer' => 'You can share your unique referral code with your friends via WhatsApp, SMS, or any other platform.'
                        ],
                        [
                            'question' => 'When will I get my referral bonus?',
                            'answer' => "You will receive your ₹{$referrerBonus} bonus in your wallet as soon as your referred friend successfully completes their first service booking."
                        ],
                        [
                            'question' => 'How can I use my wallet balance?',
                            'answer' => "You can use your wallet balance to pay up to {$walletLimitPercent}% of the total billing amount on any service booking."
                        ]
                    ],
                    'terms_and_conditions' => [
                        "The signup bonus of ₹{$signupBonus} is applicable only for new users registering for the first time.",
                        "The referral reward of ₹{$referrerBonus} will only be credited once the referred friend completes their first service.",
                        "Wallet balance cannot be transferred to another account or withdrawn as cash.",
                        "A maximum of {$walletLimitPercent}% of the total booking amount can be paid using the wallet balance per booking.",
                        "BeautyDen reserves the right to modify or cancel the referral program at any time without prior notice.",
                        "Any fraudulent activity will result in the suspension of the account and forfeiture of the wallet balance."
                    ]
                ],
                'my_referrals' => [
                    'total_count' => \App\Models\User::where('referred_by', $authUser->id)->count(),
                    'users' => \App\Models\User::where('referred_by', $authUser->id)
                        ->orderBy('id', 'desc')
                        ->get()
                        ->map(function($user) {
                            $hasCompletedBooking = \App\Models\Appointment::where('phone', $user->mobile_number)
                                ->where('status', 3)
                                ->where('created_at', '>=', $user->created_at)
                                ->exists();
                                
                            return [
                                'name' => $user->name,
                                'joined_at' => $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : null,
                                'has_completed_first_booking' => $hasCompletedBooking
                            ];
                        })
                ],
                'transactions' => $transactions
            ];

            return $this->sendResponse($success, 'Wallet history fetched successfully.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    public function saveUserAddress(Request $request): JsonResponse
    {
        $function_name = 'saveUserAddress';
        try {
            $authUser = auth('user')->user();

            if (!$authUser) {
                return $this->sendError('User not authenticated.', 401);
            }

            $validator = Validator::make($request->all(), [
                'id' => 'nullable|integer|exists:user_addresses,id',
                'address' => 'required|string|max:500',
                'home_number' => 'nullable|string|max:255',
                'street_address' => 'nullable|string',
                'landmark' => 'nullable|string|max:255',
                'city_village_name' => 'nullable|string|max:255',
                'state_name' => 'nullable|string|max:255',
                'pincode' => 'nullable|string|max:20',
                'city_id' => 'required|integer',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'type' => 'nullable|string|max:50',
                'is_default' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            $isDefault = $request->boolean('is_default');

            if ($request->id) {
                $userAddress = UserAddress::where('id', $request->id)->where('user_id', $authUser->id)->first();
                if (!$userAddress) {
                    return $this->sendError('Address not found or doesn\'t belong to you.', $this->backend_error_status);
                }
                
                $updateData = $request->only(['address', 'home_number', 'street_address', 'landmark', 'city_village_name', 'state_name', 'pincode', 'latitude', 'longitude', 'type', 'city_id']);
                if ($request->has('is_default')) {
                    $updateData['is_default'] = $isDefault;
                }
                $userAddress->update($updateData);
            } else {
                $addressCount = UserAddress::where('user_id', $authUser->id)->count();

                if ($addressCount >= 3) {
                    return $this->sendError('You can only add up to 3 addresses.', $this->validation_error_status);
                }

                $userAddress = UserAddress::create([
                    'user_id' => $authUser->id,
                    'address' => $request->address,
                    'home_number' => $request->home_number,
                    'street_address' => $request->street_address,
                    'landmark' => $request->landmark,
                    'city_village_name' => $request->city_village_name,
                    'state_name' => $request->state_name,
                    'pincode' => $request->pincode,
                    'city_id' => $request->city_id,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'type' => $request->type,
                    'is_default' => $request->has('is_default') ? $isDefault : false,
                ]);
            }

            if ($request->has('is_default') && $isDefault) {
                UserAddress::where('user_id', $authUser->id)
                    ->where('id', '!=', $userAddress->id)
                    ->update(['is_default' => false]);

                $authUser->update([
                    'city_id' => $request->city_id,
                    'address' => $request->address,
                ]);
            }

            return $this->sendResponse($userAddress, 'Address saved successfully.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    public function getUserAddresses(Request $request): JsonResponse
    {
        $function_name = 'getUserAddresses';
        try {
            $authUser = auth('user')->user();

            if (!$authUser) {
                return $this->sendError('User not authenticated.', 401);
            }

            $addresses = UserAddress::where('user_id', $authUser->id)
                ->orderBy('is_default', 'desc')
                ->orderBy('id', 'desc')
                ->get();

            return $this->sendResponse($addresses, 'Addresses fetched successfully.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    public function deleteUserAddress(Request $request): JsonResponse
    {
        $function_name = 'deleteUserAddress';
        try {
            $authUser = auth('user')->user();

            if (!$authUser) {
                return $this->sendError('User not authenticated.', 401);
            }

            $validator = Validator::make($request->all(), [
                'address_id' => 'required|integer|exists:user_addresses,id',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            $userAddress = UserAddress::where('id', $request->address_id)->where('user_id', $authUser->id)->first();
            if (!$userAddress) {
                return $this->sendError('Address not found or doesn\'t belong to you.', $this->backend_error_status);
            }

            $userAddress->delete();

            return $this->sendResponse([], 'Address deleted successfully.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    public function getTotalBookService(Request $request): JsonResponse
    {
        $function_name = 'getTotalBookService';

        try {
            $authUser = auth('user')->user();

            if (!$authUser) {
                return $this->sendError('User not authenticated.', 401);
            }

            $mobile_number = $authUser->mobile_number;

            $query = Appointment::leftJoin('cities as ct', 'ct.id', '=', 'appointments.city_id')
                ->select(
                    'appointments.id',
                    'appointments.order_number',
                    'appointments.status',
                    'appointments.user_payment_status',
                    'appointments.payment_type',
                    'appointments.appointment_date',
                    'appointments.appointment_time',
                    'appointments.service_id',
                    'appointments.assigned_to',
                    'ct.name as city_name'
                )
                ->where('appointments.phone', $mobile_number);

            if ($request->has('year') && !empty($request->year)) {
                $query->whereYear('appointments.appointment_date', $request->year);
            }

            $appointments = $query->orderByDesc('appointments.id')->get();

            $totalCompleted = $appointments->where('status', 3)->count();
            $totalPending = $appointments->where('status', 1)->count();
            $totalAssigned = $appointments->where('status', 2)->count();
            $totalRejected = $appointments->where('status', 4)->count();

            $allAssignedIds = [];
            foreach ($appointments as $app) {
                if (!empty($app->assigned_to)) {
                    $ids = explode(',', $app->assigned_to);
                    $allAssignedIds = array_merge($allAssignedIds, $ids);
                }
            }
            $allAssignedIds = array_unique($allAssignedIds);
            $teamMembers = \App\Models\TeamMember::whereIn('id', $allAssignedIds)->pluck('name', 'id')->toArray();

            $dataList = $appointments->map(function ($appointment) use ($teamMembers) {
                $serviceIds = $appointment->service_id ? explode(',', $appointment->service_id) : [];
                $totalServices = count(array_filter($serviceIds));

                $beauticianNames = null;
                if (!empty($appointment->assigned_to)) {
                    $assignedIds = explode(',', $appointment->assigned_to);
                    $names = [];
                    foreach ($assignedIds as $id) {
                        if (isset($teamMembers[$id])) {
                            $names[] = $teamMembers[$id];
                        }
                    }
                    $beauticianNames = implode(', ', $names);
                }

                return [
                    'id'                => $appointment->id,
                    'order_number'      => $appointment->order_number,
                    'status'            => (int) $appointment->status,
                    'user_payment_status'=> $appointment->user_payment_status,
                    'payment_type'      => $appointment->payment_type,
                    'appointment_date'  => $appointment->appointment_date,
                    'appointment_time'  => $appointment->appointment_time,
                    'city_name'         => $appointment->city_name,
                    'total_services'    => $totalServices,
                    'assigned_beautician' => $beauticianNames,
                ];
            });

            $responseData = [
                'total_completed' => $totalCompleted,
                'total_pending'   => $totalPending,
                'total_assigned'  => $totalAssigned,
                'total_rejected'  => $totalRejected,
                'bookings'        => $dataList,
            ];

            return $this->sendResponse($responseData, 'All booking details fetched successfully.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

     public function getBookServiceDetails(Request $request): JsonResponse
    {
        $function_name = 'getBookServiceDetails';

        try {
            $authUser = auth('user')->user();

            if (!$authUser) {
                return $this->sendError('User not authenticated.', 401);
            }

            $validator = Validator::make($request->all(), [
                'appointment_id' => 'required',
            ], [
                'appointment_id.required' => 'Appointment ID is required.',
            ]);

            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            $mobile_number = $authUser->mobile_number;
            $appointmentId = $request->appointment_id;

            $appointment = Appointment::leftJoin('cities as ct', 'ct.id', '=', 'appointments.city_id')
                ->select(
                    'appointments.*',
                    'ct.name as city_name'
                )
                ->where('appointments.id', $appointmentId)
                ->first();

            if (!$appointment) {
                return $this->sendError('Appointment not found.', 404);
            }

            // Decode the structured services_data
            $servicesData = is_string($appointment->services_data) ? json_decode($appointment->services_data, true) : $appointment->services_data;

            $beauticianNames = null;
            $beauticianDetails = null;
            if (!empty($appointment->assigned_to)) {
                $assignedIds = explode(',', $appointment->assigned_to);
                $names = \App\Models\TeamMember::whereIn('id', $assignedIds)->pluck('name')->toArray();
                $beauticianNames = implode(', ', $names);

                $firstBeautician = \App\Models\TeamMember::find($assignedIds[0]);
                if ($firstBeautician) {
                    $beauticianDetails = [
                        'name' => $firstBeautician->name,
                        'id_number' => $firstBeautician->id_number,
                        'role' => $firstBeautician->role ?? 'Beautician',
                        'experience_years' => $firstBeautician->experience_years,
                        'photo' => $firstBeautician->icon ? asset('uploads/team-members/' . $firstBeautician->icon) : asset('assets/images/default-avatar.png'),
                    ];
                }
            }

            $data = [
                'id'                     => $appointment->id,
                'order_number'           => $appointment->order_number,
                'status'                 => (int) $appointment->status,
                'user_payment_status'    => $appointment->user_payment_status,
                'payment_type'           => $appointment->payment_type,
                'company_amount'         => $appointment->company_amount,
                'city_name'              => $appointment->city_name,
                'assigned_beautician'    => $beauticianNames,
                'beautician_details'     => $beauticianDetails,
                'special_notes'          => $appointment->special_notes,
                'booking_details'        => $servicesData, // This contains client, appointment, services, and summary
                'booked_on'              => date('D, d M Y - h:i A', strtotime($appointment->created_at)),
                'created_at'             => $appointment->created_at,
                'updated_at'             => $appointment->updated_at,
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

    public function deleteAccount(Request $request): JsonResponse
    {
        $function_name = 'deleteAccount';
        try {
            $authUser = auth('user')->user();

            if (!$authUser) {
                return $this->sendError('User not authenticated.', 401);
            }

            // Invalidate Token
            $token = JWTAuth::getToken();
            if ($token) {
                JWTAuth::invalidate($token);
            }

            // Soft delete/Deactivate user
            $deletedSuffix = '_del_' . time();
            
            $authUser->update([
                'status' => 0,
                'mobile_number' => $authUser->mobile_number . $deletedSuffix,
                'email' => $authUser->email ? $authUser->email . $deletedSuffix : null,
            ]);

            auth()->guard('user')->logout();

            return $this->sendResponse([], 'Account deleted successfully.', $this->success_status);
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

    public function testFast2Sms(Request $request): JsonResponse
    {
        $function_name = 'testFast2Sms';
        try {
            $validator = Validator::make($request->all(), [
                'mobile_number' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            $mobile_number = $request->mobile_number;
            $otp = rand(100000, 999999);
            
            // Fast2SMS API Call
            $authKey = env('FAST2SMS_AUTH_KEY', ''); // Get this from .env or config
            
            $cleanedNumber = preg_replace('/\D/', '', $mobile_number);
            if (strlen($cleanedNumber) > 10) {
                $cleanedNumber = substr($cleanedNumber, -10);
            }
            
            $response = Http::withHeaders([
                'authorization' => $authKey,
            ])->get('https://www.fast2sms.com/dev/bulkV2', [
                'variables_values' => $otp,
                'route' => 'otp',
                'numbers' => $cleanedNumber,
            ]);

            if ($response->successful()) {
                Log::info("Fast2SMS OTP sent successfully to $cleanedNumber. Response: " . $response->body());
                return $this->sendResponse([
                    'otp' => $otp, 
                    'response' => $response->json(),
                    'note' => 'Please configure FAST2SMS_AUTH_KEY in .env if it returns authentication failed.'
                ], 'Test Fast2SMS sent successfully.', $this->success_status);
            } else {
                Log::error("Fast2SMS OTP send failed for $cleanedNumber. Status: " . $response->status() . " Body: " . $response->body());
                return $this->sendError('Failed to send SMS via Fast2SMS', $this->backend_error_status);
            }

        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }
}
