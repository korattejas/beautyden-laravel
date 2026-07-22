<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\ServiceCityPrice;
use Twilio\Rest\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Exception;
use Razorpay\Api\Api;

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
                'city_id'             => 'required|integer',
                'service_category_id' => 'nullable|integer',
                'service_sub_category_id' => 'nullable|integer',
                'service_id'          => 'required',
                'first_name'          => 'required|string|max:50',
                'last_name'           => 'nullable|string|max:50',
                'email'               => 'nullable|email|max:100',
                'phone'               => 'required|string|max:20',
                'quantity'            => 'nullable|integer|min:1',
                'price'               => 'nullable|numeric',
                'discount_price'      => 'nullable|numeric',
                'service_address'     => 'nullable|string',
                'appointment_date'    => 'nullable|date',
                'appointment_time'    => 'nullable',
                'notes'               => 'nullable|string',
                'status'              => 'nullable|in:0,1',
                'coupon_id'           => 'nullable|integer|exists:coupon_codes,id',
            ]);

            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            $orderNumber = '#BD' . Str::upper(Str::random(8));

            $quantity = 1;
            $subTotal = 0;
            $serviceIds = explode(',', $request->service_id);
            $services = [];

            foreach ($serviceIds as $id) {
                $service = Service::find($id);

                if ($service) {
                    $cityPrice = ServiceCityPrice::where('city_id', $request->city_id)
                        ->where('service_id', $id)
                        ->first();

                    $priceToUse = $cityPrice ? $cityPrice->price : $service->price;

                    $services[] = [
                        'type'  => 'service',
                        'name'  => $service->name,
                        'price' => $priceToUse,
                        'qty'   => $quantity,
                        'total' => $priceToUse * $quantity,
                    ];
                    $subTotal += ($priceToUse * $quantity);
                }
            }

            $discountAmount = $request->discount_price ?? 0;
            $couponCode = null;
            
            // Re-calculate or verify discount if coupon_id is provided
            if ($request->filled('coupon_id')) {
                $coupon = \App\Models\CouponCode::find($request->coupon_id);
                if ($coupon) {
                    $couponCode = $coupon->code;
                    if ($coupon->discount_type == 'percentage') {
                        $discountAmount = ($subTotal * $coupon->discount_value) / 100;
                        if ($coupon->max_discount_amount && $discountAmount > $coupon->max_discount_amount) {
                            $discountAmount = $coupon->max_discount_amount;
                        }
                    } else {
                        $discountAmount = $coupon->discount_value;
                    }
                }
            }

            $travelCharges = 0;
            $grandTotal = ($subTotal + $travelCharges) - $discountAmount;

            $servicesData = [
                'client' => [
                    'first_name' => $request->first_name,
                    'last_name'  => $request->last_name,
                    'email'      => $request->email,
                    'phone'      => $request->phone,
                ],
                'appointment' => [
                    'date'    => $request->appointment_date,
                    'time'    => $request->appointment_time,
                    'address' => $request->service_address,
                    'notes'   => $request->notes,
                ],
                'services' => $services,
                'summary' => [
                    'sub_total'        => number_format($subTotal, 2, '.', ''),
                    'travel_charges'   => number_format($travelCharges, 2, '.', ''),
                    'discount_percent' => $request->discount_percent ?? 0,
                    'discount_amount'  => number_format($discountAmount, 2, '.', ''),
                    'coupon_code'      => $couponCode,
                    'grand_total'      => number_format($grandTotal, 2, '.', ''),
                ],
            ];

            $appointmentTimeForDb = $request->appointment_time ? date("H:i:s", strtotime($request->appointment_time)) : null;

            $appointment = Appointment::create([
                'order_number'        => $orderNumber,
                'city_id'             => $request->city_id,
                'first_name'          => $request->first_name,
                'last_name'           => $request->last_name,
                'email'               => $request->email,
                'phone'               => $request->phone,
                'service_id'          => $request->service_id,
                'service_category_id' => $request->service_category_id,
                'service_sub_category_id' => $request->service_sub_category_id,
                'quantity'            => $request->quantity,
                'price'               => $subTotal,
                'discount_price'      => $discountAmount,
                'service_address'     => $request->service_address,
                'appointment_date'    => $request->appointment_date,
                'appointment_time'    => $appointmentTimeForDb,
                'special_notes'       => $request->notes,
                'services_data'       => $servicesData,
                'status'              => '1',
            ]);

            // Record Coupon Usage
            if ($appointment && $request->filled('coupon_id')) {
                \App\Models\CouponUsage::create([
                    'coupon_id' => $request->coupon_id,
                    'user_id' => auth('user')->check() ? auth('user')->id() : null,
                    'appointment_id' => $appointment->id,
                    'discount_amount' => $discountAmount,
                ]);
            }

            if (!empty($request->phone)) {
                $this->sendWhatsAppBooking($request->phone, $request->first_name, $orderNumber, $request->appointment_date, $request->appointment_time);
            }

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

    public function bookAppointmentForApp(Request $request): JsonResponse
    {
        $function_name = 'bookAppointmentForApp';

        try {
            $validator = Validator::make($request->all(), [
                'city_id'                   => 'nullable|integer',
                'items'                     => 'required|array|min:1',
                'items.*.service_master_id' => 'required|integer',
                'items.*.variant_id'        => 'nullable|integer',
                'items.*.qty'               => 'required|integer|min:1',
                'first_name'                => 'nullable|string|max:50',
                'last_name'                 => 'nullable|string|max:50',
                'email'                     => 'nullable|email|max:100',
                'phone'                     => 'nullable|string|max:20',
                'discount_price'            => 'nullable|numeric',
                'service_address'           => 'required|integer|exists:user_addresses,id',
                'appointment_date'          => 'nullable|date',
                'appointment_time'          => 'nullable',
                'notes'                     => 'nullable|string',
                'coupon_id'                 => 'nullable|integer|exists:coupon_codes,id',
                'use_wallet_balance'        => 'nullable|boolean',
                'payment_type'              => 'required|in:online,cash',
            ]);

            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            $cityId = $request->city_id;
            $firstName = $request->first_name;
            $lastName = $request->last_name;
            $email = $request->email;
            $phone = $request->phone;

            $addressObj = \App\Models\UserAddress::where('id', $request->service_address)
                ->where('user_id', auth('user')->id())
                ->first();

            if (!$addressObj) {
                return $this->sendError('Invalid address selected.', $this->validation_error_status);
            }
            $serviceAddressText = $addressObj->address;

            if ($cityId === 0 || $cityId === '0') {
                $ahmedabad = \Illuminate\Support\Facades\DB::table('cities')->where('name', 'like', '%Ahmedabad%')->first();
                if ($ahmedabad) {
                    $cityId = $ahmedabad->id;
                }
            }

            if (auth('user')->check()) {
                $authUser = auth('user')->user();
                $cityId = $cityId ?: $authUser->city_id;
                $firstName = $firstName ?: $authUser->name;
                $email = $email ?: $authUser->email;
                $phone = $phone ?: $authUser->mobile_number;
            }

            if (empty($cityId)) {
                return $this->sendError('City ID is required.', $this->validation_error_status);
            }
            if (empty($firstName)) {
                return $this->sendError('First name is required.', $this->validation_error_status);
            }
            if (empty($phone)) {
                return $this->sendError('Phone number is required.', $this->validation_error_status);
            }

            $orderNumber = '#BD' . Str::upper(Str::random(8));

            $subTotal = 0;
            $totalDuration = 0;
            $services = [];
            $serviceIdsForDB = [];

            foreach ($request->items as $item) {
                $serviceMaster = \App\Models\ServiceMaster::find($item['service_master_id']);

                if ($serviceMaster) {
                    $priceToUse = 0;
                    $variantName = '';
                    $itemDuration = 0;
                    $serviceIdsForDB[] = $item['service_master_id'];

                    if (!empty($item['variant_id'])) {
                        $variantPriceObj = \App\Models\ServiceCityVariantPrice::where('service_master_id', $item['service_master_id'])
                            ->where('variant_id', $item['variant_id'])
                            ->where('city_id', $cityId)
                            ->first();

                        $variant = \Illuminate\Support\Facades\DB::table('service_master_variants')->where('id', $item['variant_id'])->first();

                        if ($variantPriceObj) {
                            $priceToUse = $variantPriceObj->price;
                        } else if ($variant) {
                            $priceToUse = $variant->price;
                        }

                        $variantName = $variant ? $variant->name : '';
                        $itemDuration = $variant ? (int) $variant->duration : 0;

                    } else {
                        $cityMaster = \App\Models\ServiceCityMaster::where('service_master_id', $item['service_master_id'])
                            ->where('city_id', $cityId)
                            ->first();

                        if ($cityMaster) {
                            $priceToUse = $cityMaster->price;
                        } else {
                            $priceToUse = $serviceMaster->price;
                        }
                        $itemDuration = (int) $serviceMaster->duration;
                    }

                    $totalDuration += ($itemDuration * $item['qty']);

                    $services[] = [
                        'type'     => 'service',
                        'name'     => $serviceMaster->name . ($variantName ? ' - ' . $variantName : ''),
                        'price'    => $priceToUse,
                        'qty'      => $item['qty'],
                        'duration' => $itemDuration,
                        'total'    => $priceToUse * $item['qty'],
                    ];
                    $subTotal += ($priceToUse * $item['qty']);
                }
            }

            $discountAmount = $request->discount_price ?? 0;
            $couponCode = null;

            // Re-calculate or verify discount if coupon_id is provided
            if ($request->filled('coupon_id')) {
                $coupon = \App\Models\CouponCode::find($request->coupon_id);
                if ($coupon) {
                    $now = \Carbon\Carbon::now();
                    if ($coupon->status != 1) {
                        return $this->sendError('This coupon is inactive.', $this->validation_error_status);
                    }
                    if ($coupon->start_date && $now->lt($coupon->start_date)) {
                        return $this->sendError('This coupon is not yet active.', $this->validation_error_status);
                    }
                    if ($coupon->end_date && $now->gt($coupon->end_date)) {
                        return $this->sendError('This coupon has expired.', $this->validation_error_status);
                    }
                    
                    $minPurchase = (float) $coupon->min_purchase_amount;
                    if ($minPurchase > 0 && $subTotal < $minPurchase) {
                        return $this->sendError("Minimum order amount of {$minPurchase} is required for this coupon.", $this->validation_error_status);
                    }
                    
                    if ($coupon->usage_limit !== null && $coupon->usage_limit > 0) {
                        $totalUsed = \App\Models\CouponUsage::where('coupon_id', $coupon->id)->count();
                        if ($totalUsed >= $coupon->usage_limit) {
                            return $this->sendError('This coupon usage limit has been reached.', $this->validation_error_status);
                        }
                    }

                    if (auth('user')->check()) {
                        if ($coupon->usage_per_user > 0) {
                            $userUsage = \App\Models\CouponUsage::where('coupon_id', $coupon->id)
                                ->where('user_id', auth('user')->id())
                                ->count();
                            if ($userUsage >= $coupon->usage_per_user) {
                                return $this->sendError("You can only use this coupon {$coupon->usage_per_user} time(s).", $this->validation_error_status);
                            }
                        }
                        
                        if ($coupon->is_first_order_only) {
                            $userOrdersCount = \App\Models\Appointment::where('phone', auth('user')->user()->mobile_number)->count();
                            if ($userOrdersCount > 0) {
                                return $this->sendError('This coupon is valid for first-time orders only.', $this->validation_error_status);
                            }
                        }
                    }

                    $couponCode = $coupon->code;
                    if ($coupon->discount_type == 'percentage') {
                        $discountAmount = ($subTotal * $coupon->discount_value) / 100;
                        $maxDiscount = (float) $coupon->max_discount_amount;
                        if ($maxDiscount > 0 && $discountAmount > $maxDiscount) {
                            $discountAmount = $maxDiscount;
                        }
                    } else {
                        $discountAmount = (float) $coupon->discount_value;
                        if ($discountAmount > $subTotal) {
                            $discountAmount = $subTotal;
                        }
                    }
                }
            }

            $travelCharges = 0;
            $walletUsedAmount = 0;
            
            if ($request->use_wallet_balance && auth('user')->check()) {
                $user = auth('user')->user();
                
                if ($user->wallet_balance <= 0) {
                    return $this->sendError('Your wallet balance is zero.', $this->validation_error_status);
                }

                $walletLimitPercent = \App\Models\AppSetting::where('key', 'wallet_usage_limit_percent')->value('value') ?? 10;
                
                $maxUsableFromWallet = ($subTotal * $walletLimitPercent) / 100;
                
                // Allow using max of their balance or the allowed limit
                $walletUsedAmount = min($user->wallet_balance, $maxUsableFromWallet);
            }
            
            $grandTotal = ($subTotal + $travelCharges) - $discountAmount - $walletUsedAmount;

            $servicesData = [
                'client' => [
                    'first_name' => $firstName,
                    'last_name'  => $lastName,
                    'email'      => $email,
                    'phone'      => $phone,
                ],
                'appointment' => [
                    'date'    => $request->appointment_date,
                    'time'    => $request->appointment_time,
                    'address' => $serviceAddressText,
                    'notes'   => $request->notes,
                ],
                'services' => $services,
                'summary' => [
                    'sub_total'        => number_format($subTotal, 2, '.', ''),
                    'total_duration'   => $totalDuration,
                    'travel_charges'   => number_format($travelCharges, 2, '.', ''),
                    'discount_percent' => $request->discount_percent ?? 0,
                    'discount_amount'  => number_format($discountAmount, 2, '.', ''),
                    'coupon_code'      => $couponCode,
                    'wallet_used'      => number_format($walletUsedAmount, 2, '.', ''),
                    'grand_total'      => number_format($grandTotal, 2, '.', ''),
                ],
            ];

            // total quantity
            $totalQuantity = 0;
            foreach ($request->items as $item) {
                $totalQuantity += $item['qty'];
            }

            \Illuminate\Support\Facades\DB::beginTransaction();

            $appointmentTimeForDb = $request->appointment_time ? date("H:i:s", strtotime($request->appointment_time)) : null;

            $appointment = \App\Models\Appointment::create([
                'user_id'             => auth('user')->check() ? auth('user')->id() : null,
                'order_number'        => $orderNumber,
                'city_id'             => $cityId,
                'first_name'          => $firstName,
                'last_name'           => $lastName,
                'email'               => $email,
                'phone'               => $phone,
                'service_id'          => implode(',', $serviceIdsForDB),
                'quantity'            => $totalQuantity,
                'price'               => $subTotal,
                'discount_price'      => $discountAmount,
                'service_address'     => $serviceAddressText,
                'appointment_date'    => $request->appointment_date,
                'appointment_time'    => $appointmentTimeForDb,
                'special_notes'       => $request->notes,
                'services_data'       => $servicesData,
                'status'              => '1',
                'payment_type'        => $request->payment_type
            ]);

            // Record Coupon Usage
            if ($appointment && $request->filled('coupon_id')) {
                \App\Models\CouponUsage::create([
                    'coupon_id' => $request->coupon_id,
                    'user_id' => auth('user')->check() ? auth('user')->id() : null,
                    'appointment_id' => $appointment->id,
                    'discount_amount' => $discountAmount,
                ]);
            }
            
            // Record Wallet Usage
            if ($appointment && $walletUsedAmount > 0 && auth('user')->check()) {
                $user = auth('user')->user();
                $user->decrement('wallet_balance', $walletUsedAmount);
                
                \App\Models\WalletTransaction::create([
                    'user_id' => $user->id,
                    'type' => 'debit',
                    'amount' => $walletUsedAmount,
                    'description' => 'Used for Booking ' . $orderNumber,
                    'reference_id' => $appointment->id
                ]);
            }

            \Illuminate\Support\Facades\DB::commit();

            if ($request->payment_type != 'online') {
                if (!empty($phone)) {
                    $appointmentDate = $request->appointment_date;
                    $appointmentTime = $request->appointment_time;
                    
                    dispatch(function () use ($phone, $firstName, $orderNumber, $appointmentDate, $appointmentTime) {
                        try {
                            $this->sendWhatsAppBooking($phone, $firstName, $orderNumber, $appointmentDate, $appointmentTime);
                        } catch (\Exception $e) {
                            logger()->error('WhatsApp error: ' . $e->getMessage());
                        }
                    })->afterResponse();
                }

                if (auth('user')->check()) {
                    \App\Models\Cart::where('user_id', auth('user')->id())->delete();

                    $user = auth('user')->user();
                    \App\Services\NotificationService::trigger(
                        $user->id,
                        'order_placed',
                        [
                            '{order_id}' => $orderNumber,
                            '{user_name}' => $user->name ?? 'User'
                        ],
                        $appointment->id
                    );
                }
            } else {
                if (auth('user')->check()) {
                    \App\Models\Cart::where('user_id', auth('user')->id())->delete();
                }
            }

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

            $razorpayOrderId = null;
            $razorpayKey = env('RAZORPAY_KEY');

            // If there's an amount to pay and user selected online payment, create Razorpay Order
            if ($grandTotal > 0 && $request->payment_type == 'online') {
                try {
                    $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
                    $orderData = [
                        'receipt'         => (string)$orderNumber,
                        'amount'          => round($grandTotal * 100), // convert to paisa
                        'currency'        => 'INR',
                        'payment_capture' => 1 // auto capture
                    ];
                    
                    $razorpayOrder = $api->order->create($orderData);
                    $razorpayOrderId = $razorpayOrder['id'];
                } catch (\Exception $e) {
                    // Log Razorpay error and fail the booking payment status
                    logger()->error('Razorpay Order Creation Failed: ' . $e->getMessage());
                    $appointment->user_payment_status = 'failed';
                    $appointment->save();
                    
                    return $this->sendError('Payment gateway is currently down. Please try again or choose Cash on Delivery.', $this->exception_status);
                }
            }

            return $this->sendResponse(
                [
                    'appointment'  => $appointment,
                    'order_number' => $orderNumber,
                    'grand_total'  => $grandTotal,
                    'razorpay_order_id' => $razorpayOrderId,
                    'razorpay_key' => $razorpayKey
                ],
                $message,
                $this->success_status
            );
        } catch (Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            logCatchException($e, $this->controller_name, $function_name);

            return $this->sendError(
                $this->common_error_message,
                $this->exception_status
            );
        }
    }

    protected function sendWhatsAppBooking($phone, $customerName, $orderNumber, $appointmentDate = null, $appointmentTime = null)
    {
        try {
            $authKey = env('MSG91_AUTH_KEY');
            $senderNumber = env('MSG91_WHATSAPP_NUMBER');
            $templateName = 'beautyden_booking_confirmation';

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
                                        'value' => (string) $customerName
                                    ],
                                    'body_2' => [
                                        'type' => 'text',
                                        'value' => (string) $orderNumber
                                    ],
                                    'body_3' => [
                                        'type' => 'text',
                                        'value' => (string) ($appointmentDate ?? 'N/A')
                                    ],
                                    'body_4' => [
                                        'type' => 'text',
                                        'value' => (string) ($appointmentTime ?? 'N/A')
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                Log::info("MSG91 WhatsApp Booking sent successfully to $to. Response: " . $response->body());
            } else {
                Log::error("MSG91 WhatsApp Booking send failed for $to. Status: " . $response->status() . " Body: " . $response->body());
            }

            /*
            $sid    = env('TWILIO_ACCOUNT_SID');
            $token  = env('TWILIO_AUTH_TOKEN');
            $from   = env('TWILIO_WHATSAPP_FROM');
            $phone  = '6352755075';

            $client = new Client($sid, $token);
            $to = 'whatsapp:+91' . preg_replace('/\D/', '', $phone);

            $contentSid = "HXea04cd2b522a5bf3754464c4cbd5298d"; // Your approved Twilio template SID

            $contentVariables = json_encode([
                "1" => $customerName,
                "2" => $orderNumber,
                "3" => $appointmentDate ?? 'N/A',
                "4" => $appointmentTime ?? 'N/A'
            ]);

            $message = $client->messages->create($to, [
                "from" => $from,
                "contentSid" => $contentSid,
                "contentVariables" => $contentVariables
            ]);

            Log::info("WhatsApp message sent, SID: " . $message->sid);
            */
        } catch (\Exception $e) {
            Log::error("MSG91 WhatsApp Booking send exception: " . $e->getMessage());
        }
    }

    public function verifyRazorpayPayment(Request $request): JsonResponse
    {
        $function_name = 'verifyRazorpayPayment';
        try {
            $validator = Validator::make($request->all(), [
                'appointment_id' => 'required|exists:appointments,id',
                'razorpay_order_id' => 'required|string',
                'razorpay_payment_id' => 'required|string',
                'razorpay_signature' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
            
            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ];

            // Verify the signature, will throw exception if invalid
            $api->utility->verifyPaymentSignature($attributes);

            // Fetch payment details to get the amount (optional, but good practice)
            $payment = $api->payment->fetch($request->razorpay_payment_id);
            $amount = $payment['amount'] / 100; // Convert paisa back to INR

            // Record transaction in DB
            \App\Models\RazorpayTransaction::create([
                'user_id' => auth('user')->check() ? auth('user')->id() : null,
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
                'amount' => $amount,
                'currency' => 'INR',
                'status' => 'success',
                'meta_data' => ['appointment_id' => $request->appointment_id],
                'payment_details' => $payment->toArray(),
                'method' => $payment['method'] ?? 'online',
            ]);

            // Update user payment status to paid
            $appointment = \App\Models\Appointment::find($request->appointment_id);
            if ($appointment) {
                $appointment->user_payment_status = 'paid';
                $appointment->save();

                $phone = $appointment->phone;
                $firstName = $appointment->first_name;
                $orderNumber = $appointment->order_number;
                $appointmentDate = $appointment->appointment_date;
                $appointmentTime = $appointment->appointment_time;
                
                if (!empty($phone)) {
                    dispatch(function () use ($phone, $firstName, $orderNumber, $appointmentDate, $appointmentTime) {
                        try {
                            $this->sendWhatsAppBooking($phone, $firstName, $orderNumber, $appointmentDate, $appointmentTime);
                        } catch (\Exception $e) {
                            logger()->error('WhatsApp error: ' . $e->getMessage());
                        }
                    })->afterResponse();
                }

                $user = \App\Models\User::where('mobile_number', $phone)->first();
                if ($user) {
                    \App\Services\NotificationService::trigger(
                        $user->id,
                        'order_placed',
                        [
                            '{order_id}' => $orderNumber,
                            '{user_name}' => $user->name ?? 'User'
                        ],
                        $appointment->id
                    );
                }
            }

            return $this->sendResponse([], 'Payment verified successfully.', $this->success_status);

        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);

            // Update user payment status to failed if appointment exists
            if ($request->has('appointment_id')) {
                $appointment = \App\Models\Appointment::find($request->appointment_id);
                if ($appointment) {
                    $appointment->user_payment_status = 'failed';
                    $appointment->save();

                    $user = \App\Models\User::where('mobile_number', $appointment->phone)->first();
                    $userId = $user ? $user->id : null;
                    $userName = $user ? $user->name : 'User';

                    \App\Services\NotificationService::trigger($userId, 'payment_failed', [
                        '{order_id}' => $appointment->order_number,
                        '{user_name}' => $userName
                    ], $appointment->id);
                }
            }

            return $this->sendError('Payment verification failed: ' . $e->getMessage(), $this->backend_error_status);
        }
    }

    public function retryRazorpayPayment(Request $request): JsonResponse
    {
        $function_name = 'retryRazorpayPayment';
        try {
            $validator = Validator::make($request->all(), [
                'appointment_id' => 'required|exists:appointments,id',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            if (!auth('user')->check()) {
                return $this->sendError('Unauthenticated user.', 401);
            }

            $user = auth('user')->user();
            $appointment = \App\Models\Appointment::where('id', $request->appointment_id)
                ->where('phone', $user->mobile_number)
                ->first();

            if (!$appointment) {
                return $this->sendError('Appointment not found or does not belong to you.', $this->backend_error_status);
            }

            if ($appointment->user_payment_status == 'paid') {
                return $this->sendError('Payment is already completed for this appointment.', $this->validation_error_status);
            }

            if ($appointment->payment_type != 'online') {
                return $this->sendError('This is not an online payment appointment.', $this->validation_error_status);
            }

            $servicesData = $appointment->services_data ? (is_string($appointment->services_data) ? json_decode($appointment->services_data, true) : $appointment->services_data) : null;
            $grandTotal = $servicesData['summary']['grand_total'] ?? 0;

            if ($grandTotal <= 0) {
                return $this->sendError('No amount to be paid.', $this->validation_error_status);
            }

            $razorpayOrderId = null;
            $razorpayKey = env('RAZORPAY_KEY');

            try {
                $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
                $orderData = [
                    'receipt'         => (string)$appointment->order_number,
                    'amount'          => round($grandTotal * 100), // convert to paisa
                    'currency'        => 'INR',
                    'payment_capture' => 1 // auto capture
                ];
                
                $razorpayOrder = $api->order->create($orderData);
                $razorpayOrderId = $razorpayOrder['id'];
                
                // Keep it pending
                $appointment->user_payment_status = 'pending';
                $appointment->save();
            } catch (\Exception $e) {
                logger()->error('Retry Razorpay Order Creation Failed: ' . $e->getMessage());
                return $this->sendError('Payment gateway is currently down. Please try again later.', $this->exception_status);
            }

            return $this->sendResponse(
                [
                    'appointment_id' => $appointment->id,
                    'order_number' => $appointment->order_number,
                    'grand_total'  => $grandTotal,
                    'razorpay_order_id' => $razorpayOrderId,
                    'razorpay_key' => $razorpayKey
                ],
                'New Razorpay order created successfully.',
                $this->success_status
            );

        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    public function getBookingSlots(Request $request): JsonResponse
    {
        $function_name = 'getBookingSlots';
        try {
            $advanceHours = (int) (\App\Models\AppSetting::where('key', 'advance_book_hours')->value('value') ?? 6);
            $serviceTime = \App\Models\AppSetting::where('key', 'service_time')->value('value') ?? '04:00 AM - 07:00 PM';
            
            // Parse service time
            $times = explode('-', $serviceTime);
            $startTimeStr = trim($times[0]);
            $endTimeStr = count($times) > 1 ? trim($times[1]) : '07:00 PM';
        
            // Use IST for all time calculations
            $tz = 'Asia/Kolkata';

            // Target Date
            $targetDate = $request->date ? \Carbon\Carbon::parse($request->date, $tz) : \Carbon\Carbon::today($tz);
            $now = \Carbon\Carbon::now($tz);
            $cutoffTime = $now->copy()->addHours($advanceHours);
            
            // Generate Dates (365 days as requested)
            $dates = [];
            for ($i = 0; $i < 365; $i++) {
                $loopDate = \Carbon\Carbon::today($tz)->addDays($i);
                $dates[] = [
                    'date' => $loopDate->format('Y-m-d'),
                    'display_label' => $i == 0 ? 'Today' : ($i == 1 ? 'Tomorrow' : $loopDate->format('d M')),
                    'day_name' => $loopDate->format('D'),
                    'is_selected' => $loopDate->isSameDay($targetDate)
                ];
            }
        
            // Generate Slots (15 minutes interval)
            $slots = [];
            $slotStart = \Carbon\Carbon::parse($targetDate->format('Y-m-d') . ' ' . $startTimeStr, $tz);
            $slotEnd = \Carbon\Carbon::parse($targetDate->format('Y-m-d') . ' ' . $endTimeStr, $tz);
        
            while ($slotStart <= $slotEnd) {
                $slotTimeStr = $slotStart->format('h:i A'); // e.g. 08:15 AM
                $status = 'available';
                
                if ($targetDate->isToday() && $slotStart->lessThan($cutoffTime)) {
                    $status = 'disabled';
                }
                
                $slots[] = [
                    'time' => $slotTimeStr,
                    'status' => $status,
                    'offer_text' => null
                ];
        
                $slotStart->addMinutes(15);
            }
        
            $urgentContactCountDay = (int) (\App\Models\AppSetting::where('key', 'advance_urgent_contact_count_day')->value('value') ?? 1);
            $diffDays = \Carbon\Carbon::today($tz)->diffInDays($targetDate);
            
            $urgentContactData = null;
            if ($diffDays < $urgentContactCountDay) {
                $urgentContactPhone = \App\Models\AppSetting::where('key', 'whatsapp_phone_number')->value('value') ?? '9574758282';
                $urgentContactData = [
                    'text' => 'Need urgent service?',
                    'sub_text' => 'Please call or WhatsApp us on ' . $urgentContactPhone,
                    'phone' => $urgentContactPhone
                ];
            }
        
            return $this->sendResponse(
                [
                    'urgent_contact' => $urgentContactData,
                    'dates' => $dates,
                    'slots' => $slots
                ],
                'Slots retrieved successfully',
                $this->success_status
            );
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }
}
