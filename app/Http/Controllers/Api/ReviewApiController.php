<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerReview;
use App\Models\ServiceMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ReviewApiController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Api/ReviewApiController";
    }

    /**
     * Submit a new review from the App
     */
    public function submitReview(Request $request)
    {
        $function_name = 'submitReview';
        try {
            $user = Auth::guard('user')->user();
            if (!$user) {
                return $this->sendError('Unauthorized', 401);
            }

            $validator = Validator::make($request->all(), [
                'service_id' => 'required|exists:service_masters,id',
                'rating' => 'required|numeric|min:1|max:5',
                'review' => 'required|string',
                'appointment_id' => 'nullable|exists:appointments,id',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->validator_error_code);
            }

            // Get Category ID from Service
            $service = ServiceMaster::find($request->service_id);

            $review = CustomerReview::create([
                'user_id' => $user->id,
                'appointment_id' => $request->appointment_id,
                'category_id' => $service->category_id,
                'service_id' => $request->service_id,
                'customer_name' => $user->name,
                'rating' => $request->rating,
                'review' => $request->review,
                'review_date' => now(),
                'status' => 0, // Pending by default
            ]);

            return $this->sendResponse($review, 'Review submitted successfully. It will be visible after admin approval.');

        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->error_message, $this->exception_error_code);
        }
    }
}
