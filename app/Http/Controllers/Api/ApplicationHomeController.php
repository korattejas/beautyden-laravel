<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Offer;
use App\Models\City;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Exception;

class ApplicationHomeController extends Controller
{
    protected mixed $success_status, $exception_status, $backend_error_status, $validation_error_status, $common_error_message;
    protected string $controller_name;

    public function __construct()
    {
        $this->controller_name = 'API/ApplicationHomeController';
        $this->success_status = config('custom.status_code_for_success');
        $this->exception_status = config('custom.status_code_for_exception_error');
        $this->backend_error_status = config('custom.status_code_for_backend_error');
        $this->validation_error_status = config('custom.status_code_for_validation_error');
        $this->common_error_message = config('custom.common_error_message');
    }

    public function getHomePageData(Request $request): JsonResponse
    {
        $function_name = 'getHomePageData';

        try {
            $cityId = $request->input('city_id');
            $search = $request->input('search');

            // 1. User Details (Always return object or null if guest)
            $user = auth('user')->user();
            if ($user) {
                $user->update([
                    'last_login_at' => now(),
                    'last_login_ip' => $request->ip()
                ]);
            }

            $userData = $user ? [
                'id' => (int) $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'mobile_number' => $user->mobile_number,
                'city_id' => (int) $user->city_id,
                'address' => $user->address,
                'addresses' => $user->addresses()->orderBy('is_default', 'desc')->orderBy('id', 'desc')->get(),
            ] : null;

            // 2. Selected City Details
            $selectedCity = null;
            if ($cityId) {
                $selectedCity = DB::table('cities')
                    ->select('id', 'name', DB::raw('CONCAT("' . asset('uploads/city') . '/", icon) AS icon'))
                    ->where('id', $cityId)
                    ->first();
            }

            // 3. Offers (Sliders/Banners)
            $offers = Offer::where('status', 1)
                ->orderBy('priority', 'asc')
                ->get()
                ->map(function ($offer) {
                    $media = [];
                    if (!empty($offer->media)) {
                        $dir = $offer->media_type == 'image' ? 'uploads/offers/images/' : 'uploads/offers/videos/';
                        foreach ($offer->media as $file) {
                            $media[] = asset($dir . $file);
                        }
                    }
                    $offer->media_urls = $media;
                    return $offer;
                });

            // 4. Category List (Service Categories)
            $categories = DB::table('service_categories')
                ->select(
                    'id', 
                    'name', 
                    DB::raw('CONCAT("' . asset('uploads/service-category') . '/", icon) AS icon'), 
                    'description', 
                    'is_popular'
                )
                ->where('status', 1)
                ->orderByDesc('is_popular')
                ->get();

            // 5. City Wise Services (Search or Popular)
            $servicesQuery = DB::table('services as s')
                ->join('service_categories as c', 's.category_id', '=', 'c.id')
                ->where('s.status', 1);

            // If city selected, join with city prices to get specific prices
            if ($cityId) {
                $servicesQuery->join('service_city_prices as scp', function($join) use ($cityId) {
                    $join->on('scp.service_id', '=', 's.id')
                         ->where('scp.city_id', '=', $cityId)
                         ->where('scp.status', '=', 1);
                })
                ->select(
                    's.id', 
                    's.name', 
                    'scp.price as base_price', 
                    'scp.discount_price',
                    's.duration', 
                    's.rating', 
                    's.reviews', 
                    DB::raw('CONCAT("' . asset('uploads/service') . '/", s.icon) AS icon'),
                    'c.name as category_name'
                );
            } else {
                $servicesQuery->select(
                    's.id', 
                    's.name', 
                    's.price as base_price', 
                    's.discount_price',
                    's.duration', 
                    's.rating', 
                    's.reviews', 
                    DB::raw('CONCAT("' . asset('uploads/service') . '/", s.icon) AS icon'),
                    'c.name as category_name'
                );
            }

            if (!empty($search)) {
                $servicesQuery->where(function($q) use ($search) {
                    $q->where('s.name', 'like', "%$search%")
                      ->orWhere('c.name', 'like', "%$search%")
                      ->orWhere('s.description', 'like', "%$search%");
                });
            } else {
                $servicesQuery->where('s.is_popular', 1)->limit(10);
            }

            $services = $servicesQuery->get();

            // 6. Reviews
            $reviews = DB::table('customer_reviews as r')
                ->leftJoin('service_categories as c', 'r.category_id', '=', 'c.id')
                ->select(
                    'r.id',
                    'r.customer_name',
                    DB::raw('CONCAT("' . asset('uploads/review/customer-photos') . '/", r.customer_photo) AS customer_photo'),
                    'r.rating',
                    'r.review',
                    'r.review_date',
                    'c.name as category_name',
                    'r.photos'
                )
                ->where('r.status', 1)
                ->orderByDesc('r.is_popular')
                ->limit(6)
                ->get()
                ->map(function ($review) {
                    $photos = $review->photos ? json_decode($review->photos, true) : [];
                    $review->media_photos = array_map(function ($photo) {
                        return asset('uploads/review/photos/' . $photo);
                    }, $photos);
                    return $review;
                });

            // 7. Product Brands
            $productBrands = DB::table('product_brands')
                ->select(
                    'id',
                    'name',
                    DB::raw('CONCAT("' . asset('uploads/product-brand') . '/", icon) AS icon')
                )
                ->where('status', 1)
                ->orderBy('name', 'ASC')
                ->get();

            // 8. All Cities (for selection)
            $allCities = DB::table('cities')
                ->select('id', 'name', DB::raw('CONCAT("' . asset('uploads/city') . '/", icon) AS icon'))
                ->where('status', 1)
                ->orderBy('name', 'asc')
                ->get();

            $responseData = [
                'user' => $userData,
                'selected_city' => $selectedCity,
                'offers' => $offers ?? [],
                'categories' => $categories ?? [],
                'services' => $services ?? [],
                'reviews' => $reviews ?? [],
                'brands' => $productBrands ?? [],
                'all_cities' => $allCities ?? []
            ];

            return $this->sendResponse(
                $responseData,
                'Home page data retrieved successfully',
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
