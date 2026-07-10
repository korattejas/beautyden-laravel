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
            $user = auth('user')->user();
            if (!$user) {
                return $this->sendError('Unauthorized', 401);
            }

            $validator = Validator::make($request->all(), [
                'appointment_id' => 'required|exists:appointments,id',
                'overall_rating' => 'required|numeric|min:1|max:5',
                'category_ratings' => 'required|array',
                'category_ratings.*.category_id' => 'required|integer',
                'category_ratings.*.rating' => 'required|numeric|min:1|max:5',
                'review' => 'nullable|string',
                'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->validator_error_code);
            }

            // Get Appointment details
            $appointment = \App\Models\Appointment::find($request->appointment_id);

            if ($appointment->phone != $user->mobile_number) {
                return $this->sendError('This appointment does not belong to you.', 403);
            }

            if ((int)$appointment->status !== 3) {
                return $this->sendError('You can only review completed appointments.', 403);
            }

            $existingReview = \App\Models\CustomerReview::where('appointment_id', $request->appointment_id)->exists();
            if ($existingReview) {
                return $this->sendError('You have already reviewed this appointment.', 409);
            }

            // Handle multiple photos
            $photoNames = [];
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $image) {
                    $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('uploads/review/photos/'), $imageName);
                    $photoNames[] = $imageName;
                }
            }

            // Insert review for each category
            $reviews = [];
            foreach ($request->category_ratings as $catRating) {
                $reviews[] = CustomerReview::create([
                    'user_id' => $user->id,
                    'appointment_id' => $request->appointment_id,
                    'category_id' => $catRating['category_id'],
                    'service_id' => 0, // 0 signifies category-level review
                    'customer_name' => $user->name,
                    'rating' => $catRating['rating'],
                    'overall_rating' => $request->overall_rating,
                    'review' => $request->review,
                    'review_date' => now()->toDateString(),
                    'photos' => $photoNames,
                    'status' => 0, // Pending by default
                ]);
            }

            return $this->sendResponse($reviews, 'Review submitted successfully. It will be visible after admin approval.');

        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->error_message, $this->exception_error_code);
        }
    }

    /**
     * Get submitted review for a specific appointment
     */
    public function getAppointmentReview(Request $request)
    {
        $function_name = 'getAppointmentReview';
        try {
            $user = auth('user')->user();
            if (!$user) {
                return $this->sendError('Unauthorized', 401);
            }

            $validator = Validator::make($request->all(), [
                'appointment_id' => 'required|exists:customer_reviews,appointment_id',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->validator_error_code);
            }

            $reviews = \App\Models\CustomerReview::query()
                ->leftJoin('service_categories as sc', 'sc.id', '=', 'customer_reviews.category_id')
                ->where('customer_reviews.appointment_id', $request->appointment_id)
                ->where('customer_reviews.user_id', $user->id)
                ->select('customer_reviews.*', 'sc.name as category_name')
                ->get();

            if ($reviews->isEmpty()) {
                return $this->sendError('No review found for this appointment.', 404);
            }

            $firstReview = $reviews->first();
            
            $summary = $this->getAppointmentSummaryData($firstReview->appointment_id);

            $photos = $firstReview->photos ? $firstReview->photos : [];
            $fullPhotoUrls = array_map(function ($photo) {
                return asset('uploads/review/photos/' . $photo);
            }, $photos);

            $categoryRatings = [];
            foreach ($reviews as $review) {
                $categoryRatings[] = [
                    'category_id' => $review->category_id,
                    'category_name' => $review->category_name,
                    'rating' => (float) $review->rating,
                ];
            }

            $data = [
                'appointment_id' => $firstReview->appointment_id,
                'appointment_summary' => $summary,
                'overall_rating' => (float) $firstReview->overall_rating,
                'review' => $firstReview->review,
                'photos' => $fullPhotoUrls,
                'category_ratings' => $categoryRatings,
            ];

            return $this->sendResponse($data, 'Review details fetched successfully.');

        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->error_message, $this->exception_error_code);
        }
    }

    /**
     * Get Appointment Summary for Review Screen
     */
    public function getAppointmentSummaryForReview(Request $request)
    {
        $function_name = 'getAppointmentSummaryForReview';
        try {
            $user = auth('user')->user();
            if (!$user) {
                return $this->sendError('Unauthorized', 401);
            }

            $validator = Validator::make($request->all(), [
                'appointment_id' => 'required|exists:appointments,id',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->validator_error_code);
            }

            $summary = $this->getAppointmentSummaryData($request->appointment_id);
            if (!$summary) {
                return $this->sendError('Appointment not found.', 404);
            }

            return $this->sendResponse($summary, 'Appointment summary fetched successfully.');

        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->error_message, $this->exception_error_code);
        }
    }

    /**
     * Helper method to get appointment summary data
     */
    private function getAppointmentSummaryData($appointmentId)
    {
        $appointment = \App\Models\Appointment::find($appointmentId);
        if (!$appointment) return null;

        $totalServices = 0;
        $servicesData = is_string($appointment->services_data) ? json_decode($appointment->services_data, true) : $appointment->services_data;
        if (isset($servicesData['services'])) {
            $totalServices = count($servicesData['services']);
        }

        $beautician = null;
        $beauticianName = 'N/A';
        if (!empty($appointment->assigned_to)) {
            $assignedIds = explode(',', $appointment->assigned_to);
            $firstBeautician = \App\Models\TeamMember::find($assignedIds[0]);
            if ($firstBeautician) {
                $beauticianName = $firstBeautician->name;
                $beautician = [
                    'name' => $firstBeautician->name,
                    'id_number' => $firstBeautician->id_number,
                    'role' => $firstBeautician->role ?? 'Beautician',
                    'experience_years' => $firstBeautician->experience_years,
                    'photo' => $firstBeautician->icon ? asset('uploads/team-members/' . $firstBeautician->icon) : asset('assets/images/default-avatar.png'),
                ];
            }
        }

        $paymentMode = ucfirst($appointment->payment_type ?? 'Cash');

        return [
            'appointment_date' => date('d M, Y', strtotime($appointment->appointment_date)),
            'appointment_time' => date('h:i A', strtotime($appointment->appointment_time)),
            'order_number' => $appointment->order_number,
            'status_text' => 'Completed',
            'total_services' => $totalServices,
            'assigned_beautician_name' => $beauticianName,
            'booked_on' => date('D, d M Y - h:i A', strtotime($appointment->created_at)),
            'payment_mode' => $paymentMode,
            'beautician_details' => $beautician,
        ];
        return $summary;
    }


    /**
     * Get reviews for a specific category
     */
    public function getCategoryReviews(Request $request)
    {
        $function_name = 'getCategoryReviews';
        try {
            $validator = Validator::make($request->all(), [
                'category_id' => 'required|exists:service_categories,id',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->validator_error_code);
            }

            $limit = $request->limit ?? 50;

            $customerReviewsQuery = \Illuminate\Support\Facades\DB::table('customer_reviews as r')
                ->select(
                    'r.id',
                    'r.customer_name',
                    \Illuminate\Support\Facades\DB::raw('CONCAT("' . asset('uploads/review/customer-photos') . '/", r.customer_photo) AS customer_photo'),
                    'r.rating',
                    'r.review',
                    'r.review_date'
                )
                ->where('r.category_id', $request->category_id)
                ->where('r.status', 1)
                ->orderByDesc('r.is_popular')
                ->orderByDesc('r.review_date')
                ->limit($limit)
                ->get();

            // Calculate overall rating and count for this category
            $realReviewsCount = \Illuminate\Support\Facades\DB::table('customer_reviews')->where('category_id', $request->category_id)->where('status', 1)->count();
            $realRatingAvg = \Illuminate\Support\Facades\DB::table('customer_reviews')->where('category_id', $request->category_id)->where('status', 1)->avg('rating');

            $data = [
                'total_reviews' => (int) $realReviewsCount,
                'average_rating' => (string) ($realReviewsCount > 0 ? round($realRatingAvg, 1) : 0),
                'reviews' => $customerReviewsQuery
            ];

            return $this->sendResponse($data, 'Category reviews retrieved successfully');

        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->error_message, $this->exception_error_code);
        }
    }
}
