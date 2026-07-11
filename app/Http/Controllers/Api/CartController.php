<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Cart;
use App\Models\ServiceMaster;
use App\Models\ServiceCityMaster;
use App\Models\ServiceCityVariantPrice;
use Illuminate\Support\Facades\Validator;
use Exception;

class CartController extends Controller
{
    protected mixed $success_status, $exception_status, $backend_error_status, $validation_error_status, $common_error_message;
    protected string $controller_name;

    public function __construct()
    {
        $this->controller_name = 'API/CartController';
        $this->success_status = config('custom.status_code_for_success');
        $this->exception_status = config('custom.status_code_for_exception_error');
        $this->backend_error_status = config('custom.status_code_for_backend_error');
        $this->validation_error_status = config('custom.status_code_for_validation_error');
        $this->common_error_message = config('custom.common_error_message');
    }

    public function addToCart(Request $request): JsonResponse
    {
        try {
            // Assuming auth middleware sets the user id
            $userId = $request->user_id; 

            if (!$userId) {
                return $this->sendError('User ID is required.', $this->validation_error_status);
            }

            $validator = Validator::make($request->all(), [
                'service_id' => 'required|exists:service_masters,id',
                'variant_id' => 'nullable|exists:service_master_variants,id',
                'qty' => 'nullable|integer|min:1',
                'city_id' => 'required|exists:cities,id'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            $service = ServiceMaster::find($request->service_id);
            if ($service->has_variants == 1 && !$request->variant_id) {
                return $this->sendError('This service has variants. Please select a variant.', $this->validation_error_status);
            }

            $qty = $request->qty ?? 1;

            $cartItem = Cart::where('user_id', $userId)
                ->where('service_master_id', $request->service_id)
                ->where('variant_id', $request->variant_id)
                ->where('city_id', $request->city_id)
                ->first();

            if ($cartItem) {
                $cartItem->qty += $qty;
                $cartItem->save();
            } else {
                Cart::create([
                    'user_id' => $userId,
                    'service_master_id' => $request->service_id,
                    'variant_id' => $request->variant_id,
                    'city_id' => $request->city_id,
                    'qty' => $qty
                ]);
            }

            return $this->sendResponse([], 'Item added to cart successfully', $this->success_status);

        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, 'addToCart');
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    public function getCart(Request $request): JsonResponse
    {
        try {
            $userId = $request->user_id;
            $cityId = $request->city_id;

            if (!$userId || !$cityId) {
                return $this->sendError('User ID and City ID are required.', $this->validation_error_status);
            }

            $cartItems = Cart::with(['service', 'variant'])->where('user_id', $userId)->where('city_id', $cityId)->get();

            if ($cartItems->isEmpty()) {
                return $this->sendResponse(['cart_items' => [], 'bill_summary' => null], 'Cart is empty', $this->success_status);
            }

            $formattedItems = [];
            $subTotal = 0;
            $totalDiscount = 0;

            foreach ($cartItems as $item) {
                $service = $item->service;
                
                $price = 0;
                $discountPrice = 0;
                $discountPercentage = 0;
                $duration = null;

                if ($item->variant_id) {
                    $variantPrice = ServiceCityVariantPrice::where('service_master_id', $item->service_master_id)
                        ->where('variant_id', $item->variant_id)
                        ->where('city_id', $cityId)
                        ->where('is_available', 1)
                        ->first();

                    if ($variantPrice) {
                        $price = (int) $variantPrice->price;
                        $discountPrice = (int) round($price + ($price * $variantPrice->discount_price / 100));
                        $discountPercentage = (int) $variantPrice->discount_price;
                    }
                    $duration = $item->variant->duration ?? $service->duration;
                } else {
                    $cityService = ServiceCityMaster::where('service_master_id', $item->service_master_id)
                        ->where('city_id', $cityId)
                        ->where('status', 1)
                        ->first();

                    if ($cityService) {
                        $price = (int) $cityService->price;
                        $discountPrice = (int) round($price + ($price * $cityService->discount_price / 100));
                        $discountPercentage = (int) $cityService->discount_price;
                    }
                    $duration = $service->duration;
                }

                $itemSubTotal = $price * $item->qty;
                $itemDiscount = ($discountPrice - $price) * $item->qty;

                $subTotal += $itemSubTotal;
                $totalDiscount += $itemDiscount;

                $formattedItems[] = [
                    'cart_id' => $item->id,
                    'service_id' => $service->id,
                    'variant_id' => $item->variant_id,
                    'name' => $item->variant_id ? $service->name . ' - ' . $item->variant->name : $service->name,
                    'icon' => $item->variant_id && $item->variant->thumbnail_image 
                        ? asset('uploads/service-variant/' . $item->variant->thumbnail_image) 
                        : asset('uploads/service/' . $service->icon),
                    'duration' => $duration,
                    'qty' => (string) $item->qty,
                    'price' => $price,
                    'discount_price' => $discountPrice,
                    'discount_percentage' => $discountPercentage
                ];
            }

            $billSummary = [
                'sub_total' => $subTotal,
                'total_discount' => $totalDiscount,
                'final_amount' => $subTotal
            ];

            return $this->sendResponse([
                'cart_items' => $formattedItems,
                'bill_summary' => $billSummary
            ], 'Cart retrieved successfully', $this->success_status);

        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, 'getCart');
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    public function updateCartItem(Request $request): JsonResponse
    {
        try {
            $userId = $request->user_id;
            
            $validator = Validator::make($request->all(), [
                'cart_id' => 'required|exists:carts,id',
                'action' => 'required|in:increment,decrement,delete'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            $cartItem = Cart::where('id', $request->cart_id)->where('user_id', $userId)->first();
            if (!$cartItem) {
                return $this->sendError('Cart item not found', $this->validation_error_status);
            }

            if ($request->action == 'delete') {
                $cartItem->delete();
            } elseif ($request->action == 'decrement') {
                if ($cartItem->qty > 1) {
                    $cartItem->qty -= 1;
                    $cartItem->save();
                } else {
                    $cartItem->delete();
                }
            } elseif ($request->action == 'increment') {
                $cartItem->qty += 1;
                $cartItem->save();
            }

            // Return updated cart directly
            $request->merge(['city_id' => $cartItem->city_id]);
            return $this->getCart($request);

        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, 'updateCartItem');
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }
}
