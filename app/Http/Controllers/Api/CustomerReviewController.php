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

    public function getCustomerReviews(Request $request): JsonResponse
    {
        $function_name = 'getCustomerReviews';

        try {
            $query = DB::table('customer_reviews as r')
                ->leftJoin('services as s', 'r.service_id', '=', 's.id')
                ->select(
                    'r.id',
                    'r.service_id',
                    's.name as service_name',
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
                ->where('r.status', 1);

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('r.customer_name', 'like', "%{$search}%")
                        ->orWhere('r.review', 'like', "%{$search}%")
                        ->orWhere('r.review_date', 'like', "%{$search}%")
                        ->orWhere('r.helpful_count', 'like', "%{$search}%")
                        ->orWhere('r.is_popular', 'like', "%{$search}%")
                        ->orWhere('s.name', 'like', "%{$search}%");
                });
            }

            if ($request->has('service_id') && !empty($request->service_id)) {
                $query->where('r.service_id', $request->service_id);
            }

            if ($request->has('with_photos') && $request->with_photos) {
                $query->whereNotNull('r.photos')
                    ->where('r.photos', '!=', '[]');
            }

            if ($request->has('with_video') && $request->with_video) {
                $query->whereNotNull('r.video')
                    ->where('r.video', '!=', '');
            }

            if ($request->has('rating') && is_numeric($request->rating)) {
                $query->where('r.rating', '>=', $request->rating);
            }

            $perPage = $request->per_page ?? 9;
            $page = $request->page ?? 1;

            $reviews = $query->orderByDesc('r.is_popular')
                ->paginate($perPage, ['*'], 'page', $page)
                ->through(function ($review) {
                    $photos = $review->photos ? json_decode($review->photos, true) : [];
                    $review->photos = array_map(function ($photo) {
                        return asset('uploads/review/photos/' . $photo);
                    }, $photos);
                    return $review;
                });

            if ($reviews->total() === 0) {
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
