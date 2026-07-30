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
                'photos' => 'nullable|array|max:10',
                'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            ], [
                'photos.max' => 'You can upload a maximum of 10 photos.',
                'photos.*.max' => 'Each photo must not be greater than 2MB.',
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
     * Get data for the "Write a Review" screen (Appointment Summary, Beautician Details, Services, and Submitted Review)
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
                'appointment_id' => 'required|exists:appointments,id',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->validator_error_code);
            }

            $appointment = \App\Models\Appointment::find($request->appointment_id);

            // 1. Appointment Summary & Beautician Details
            $totalServices = 0;
            $servicesData = is_string($appointment->services_data) ? json_decode($appointment->services_data, true) : $appointment->services_data;
            
            $servicesList = [];
            if (isset($servicesData['services'])) {
                $totalServices = count($servicesData['services']);
                $servicesList = $servicesData['services'];
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
                        'photo' => $firstBeautician->icon ? asset('uploads/team-member/' . $firstBeautician->icon) : asset('assets/images/default-avatar.png'),
                    ];
                }
            }

            $paymentMode = ucfirst($appointment->payment_type ?? 'Cash');

            $summary = [
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

            // 2. Services List for Rating (Unique by Category)
            $rateServices = [];
            $seenCategories = [];

            if (!empty($appointment->service_id)) {
                $serviceIds = array_unique(explode(',', $appointment->service_id));
                $services = \App\Models\ServiceMaster::whereIn('id', $serviceIds)->with('category')->get();
                foreach ($services as $serviceRecord) {
                    $categoryId = $serviceRecord->category_id;
                    if ($categoryId && !in_array($categoryId, $seenCategories)) {
                        $rateServices[] = [
                            'category_name' => $serviceRecord->category ? $serviceRecord->category->name : '',
                            'category_id' => $categoryId,
                        ];
                        $seenCategories[] = $categoryId;
                    }
                }
            } else {
                // Fallback in case service_id column is empty
                foreach ($servicesList as $s) {
                    $serviceId = $s['service_id'] ?? ($s['id'] ?? 0);
                    $categoryId = $s['category_id'] ?? 0;
                    $categoryName = $s['category_name'] ?? '';
                    $serviceName = $s['name'] ?? 'Service';

                    if (empty($categoryId) || empty($categoryName)) {
                        $serviceRecord = null;
                        if (!empty($serviceId)) {
                            $serviceRecord = \App\Models\ServiceMaster::find($serviceId);
                        } else if (!empty($serviceName)) {
                            $serviceRecord = \App\Models\ServiceMaster::where('name', $serviceName)->first();
                            if ($serviceRecord) {
                                $serviceId = $serviceRecord->id;
                            }
                        }

                        if ($serviceRecord) {
                            $categoryId = $serviceRecord->category_id;
                            $cat = \App\Models\ServiceCategory::find($categoryId);
                            if ($cat) {
                                $categoryName = $cat->name;
                            }
                        }
                    }

                    if ($categoryId && !in_array($categoryId, $seenCategories)) {
                        $rateServices[] = [
                            'category_name' => $categoryName,
                            'category_id' => $categoryId,
                        ];
                        $seenCategories[] = $categoryId;
                    }
                }
            }

            // 3. Submitted Review Data (if exists)
            $reviews = \App\Models\CustomerReview::query()
                ->leftJoin('service_categories as sc', 'sc.id', '=', 'customer_reviews.category_id')
                ->where('customer_reviews.appointment_id', $request->appointment_id)
                ->where('customer_reviews.user_id', $user->id)
                ->select('customer_reviews.*', 'sc.name as category_name')
                ->get();

            $submittedReview = null;
            $existingRatings = [];
            
            if ($reviews->isNotEmpty()) {
                $firstReview = $reviews->first();
                $approved_photos = $firstReview->approved_photos ? (is_string($firstReview->approved_photos) ? json_decode($firstReview->approved_photos, true) : $firstReview->approved_photos) : [];
                $fullPhotoUrls = array_map(function ($photo) {
                    return asset('uploads/review/photos/' . $photo);
                }, $approved_photos);

                foreach ($reviews as $review) {
                    $existingRatings[$review->category_id] = (float) $review->rating;
                }

                $submittedReview = [
                    'overall_rating' => (float) $firstReview->overall_rating,
                    'review' => $firstReview->review,
                    'photos' => $fullPhotoUrls,
                ];
            }

            // Append rating to services_to_rate
            foreach ($rateServices as &$rs) {
                $rs['rating'] = isset($existingRatings[$rs['category_id']]) ? $existingRatings[$rs['category_id']] : null;
            }

            $data = [
                'appointment_id' => $request->appointment_id,
                'appointment_summary' => $summary,
                'services_to_rate' => $rateServices,
                'submitted_review_status' => $reviews->isNotEmpty(),
                'submitted_review' => $submittedReview,
            ];

            return $this->sendResponse($data, 'Review screen data fetched successfully.');

        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->error_message, $this->exception_error_code);
        }
    }


    /**
     * Get Appointment Summary For Review
     */
    public function getAppointmentSummaryForReview(Request $request)
    {
        return $this->getAppointmentReview($request);
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

            $category = \Illuminate\Support\Facades\DB::table('service_categories')
                ->where('id', $request->category_id)
                ->first();

            $showAll = false;
            if ($category) {
                $categoryName = strtolower($category->name);
                if (str_contains($categoryName, 'bridal') || str_contains($categoryName, 'pre wedding') || str_contains($categoryName, 'pre-wedding')) {
                    $showAll = true;
                }
            }

            if ($showAll) {
                $lookbookCategories = \Illuminate\Support\Facades\DB::table('category_lookbooks')
                    ->join('service_categories', 'category_lookbooks.category_id', '=', 'service_categories.id')
                    ->where('category_lookbooks.status', 1)
                    ->where('service_categories.status', 1)
                    ->whereNotNull('category_lookbooks.photos')
                    ->where('category_lookbooks.photos', '!=', '')
                    ->where('category_lookbooks.photos', '!=', '[]')
                    ->select('category_lookbooks.category_id', 'service_categories.name as category_name')
                    ->distinct()
                    ->get()
                    ->map(function ($item) {
                        return [
                            'category_id' => $item->category_id,
                            'category_name' => $item->category_name
                        ];
                    })
                    ->values()
                    ->all();

                $portfolioCategories = \Illuminate\Support\Facades\DB::table('portfolios')
                    ->join('service_categories', 'portfolios.category_id', '=', 'service_categories.id')
                    ->where('portfolios.status', 1)
                    ->where('service_categories.status', 1)
                    ->whereNotNull('portfolios.photos')
                    ->where('portfolios.photos', '!=', '')
                    ->where('portfolios.photos', '!=', '[]')
                    ->select('portfolios.category_id', 'service_categories.name as category_name')
                    ->distinct()
                    ->get()
                    ->map(function ($item) {
                        return [
                            'category_id' => $item->category_id,
                            'category_name' => $item->category_name
                        ];
                    })
                    ->values()
                    ->all();

                $data = [
                    'is_bridal_prewedding' => true,
                    'portfolio_categories' => $portfolioCategories,
                    'lookbook_categories' => $lookbookCategories
                ];

                return $this->sendResponse(
                    $data,
                    'Categories for bridal/pre-wedding retrieved successfully'
                );
            }

            $customerReviewsQuery = \Illuminate\Support\Facades\DB::table('customer_reviews as r')
                ->select(
                    'r.id',
                    'r.customer_name',
                    \Illuminate\Support\Facades\DB::raw('CONCAT("' . asset('uploads/review/customer-photos') . '/", r.customer_photo) AS customer_photo'),
                    'r.rating',
                    'r.review',
                    'r.review_date'
                )
                ->when(!$showAll, function ($q) use ($request) {
                    return $q->where('r.category_id', $request->category_id);
                })
                ->where('r.status', 1)
                ->orderByDesc('r.is_popular')
                ->orderByDesc('r.review_date')
                ->limit($limit)
                ->get();

            // Calculate overall rating and count
            $reviewsStatsQuery = \Illuminate\Support\Facades\DB::table('customer_reviews')
                ->where('status', 1);

            if (!$showAll) {
                $reviewsStatsQuery->where('category_id', $request->category_id);
            }

            $realReviewsCount = (clone $reviewsStatsQuery)->count();
            $realRatingAvg = (clone $reviewsStatsQuery)->avg('rating');

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
