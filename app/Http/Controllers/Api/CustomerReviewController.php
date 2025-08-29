<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Exception;

class CustomerReviewController extends Controller
{
    protected mixed $success_status, $exception_status, $backend_error_status, $validation_error_status, $common_error_message;

    protected string $controller_name;

    public function __construct()
    {
        $this->controller_name = 'API/CustomerReviewController';
        $this->success_status = config('custom.status_code_for_success');
        $this->exception_status = config('custom.status_code_for_exception_error');
        $this->backend_error_status = config('custom.status_code_for_backend_error');
        $this->validation_error_status = config('custom.status_code_for_validation_error');
        $this->common_error_message = config('custom.common_error_message');
    }

    public function getCustomerReviews(): JsonResponse
    {
        $function_name = 'getCustomerReviews';

        try {
            $reviews = DB::table('customer_reviews as r')
                ->select(
                    'r.id',
                    'r.service_id',
                    'r.customer_name',
                    DB::raw('CONCAT("' . asset('uploads/review/customer-photos') . '/", r.customer_photo) AS customer_photo'),
                    'r.rating',
                    'r.review',
                    'r.review_date',
                    'r.helpful_count',
                    DB::raw('CONCAT("' . asset('uploads/review/videos') . '/", r.video) AS video'),
                    'r.is_popular',
                    'r.created_at',
                    'r.updated_at',
                    'r.photos'
                )
                ->where('r.status', 1)
                ->orderByDesc('r.is_popular')
                ->get()
                ->map(function ($review) {
                    $photos = $review->photos ? json_decode($review->photos, true) : [];
                    $review->photos = array_map(function ($photo) {
                        return asset('uploads/review/photos/' . $photo);
                    }, $photos);
                    return $review;
                });
            if ($reviews->isEmpty()) {
                return $this->sendError('No customer review found.', $this->backend_error_status);
            }

            return $this->sendResponse(
                $reviews,
                'Customer reviews retrieved successfully',
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
}
