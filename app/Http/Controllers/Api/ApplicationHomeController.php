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

            // 1. User Details & Subscription
            $user = auth('user')->user();
            $userData = null;
            if ($user) {
                $user->update([
                    'last_login_at' => now(),
                    'last_login_ip' => $request->ip()
                ]);

                $addresses = $user->addresses()
                    ->select('id', 'user_id', 'address', 'latitude', 'longitude', 'type', 'is_default')
                    ->orderBy('is_default', 'desc')
                    ->orderBy('id', 'desc')
                    ->get();

                // Check active Elite subscription
                $subscription = DB::table('user_subscriptions as us')
                    ->join('membership_plans as mp', 'us.plan_id', '=', 'mp.id')
                    ->where('us.user_id', $user->id)
                    ->where('us.status', 1)
                    ->where('us.end_date', '>=', now())
                    ->select('us.id', 'us.plan_id', 'us.end_date', 'mp.name as plan_name')
                    ->first();

                $userData = [
                    'id' => (int) $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'mobile_number' => $user->mobile_number,
                    'city_id' => (int) $user->city_id,
                    'address' => $user->address,
                    'active_address' => $addresses->where('is_default', 1)->first(),
                    'addresses' => $addresses,
                    'active_subscription' => $subscription
                ];
            }

            // 2. Offers (Sliders/Banners)
            $offers = Offer::where('status', 1)
                ->select(
                    'id',
                    'title',
                    'media',
                    'position',
                    'media_type',
                    'priority',
                    'created_at'
                )
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

            // 3. Membership Plans
            $membershipPlans = DB::table('membership_plans')
                ->select('id', 'name', 'description', 'price', 'discount_percentage', 'duration_months')
                ->where('status', 1)
                ->get();

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

            // 5. City Wise Services & Trending Services
            $servicesQuery = DB::table('service_masters as s')
                ->where('s.status', 1);

            if ($cityId) {
                $servicesQuery->join('service_city_masters as scm', function($join) use ($cityId) {
                    $join->on('scm.service_master_id', '=', 's.id')
                         ->where('scm.city_id', '=', $cityId)
                         ->where('scm.status', '=', 1);
                })
                ->select(
                    's.id', 
                    's.name', 
                    's.category_id',
                    'scm.price as base_price', 
                    'scm.discount_price',
                    's.duration', 
                    's.rating', 
                    's.reviews', 
                    DB::raw('CONCAT("' . asset('uploads/service') . '/", s.icon) AS icon')
                );
            } else {
                $servicesQuery->select(
                    's.id', 
                    's.name', 
                    's.category_id',
                    's.price as base_price', 
                    's.discount_price',
                    's.duration', 
                    's.rating', 
                    's.reviews', 
                    DB::raw('CONCAT("' . asset('uploads/service') . '/", s.icon) AS icon')
                );
            }

            // For search results
            $services = [];
            if (!empty($search)) {
                $services = (clone $servicesQuery)->where(function($q) use ($search) {
                    $q->where('s.name', 'like', "%$search%")
                      ->orWhere('s.description', 'like', "%$search%");
                })->get();
            }

            // Group trending services by category for tabs
            $trendingServices = (clone $servicesQuery)
                ->where('s.is_popular', 1)
                ->get()
                ->groupBy('category_id');

            $trendingData = [];
            foreach ($trendingServices as $catId => $items) {
                $category = $categories->where('id', $catId)->first();
                if ($category) {
                    $trendingData[] = [
                        'category_id' => $catId,
                        'category_name' => $category->name,
                        'services' => $items
                    ];
                }
            }

            // 6. Reviews (Optimized)
            $reviews = DB::table('customer_reviews as r')
                ->select('r.id', 'r.customer_name', 'r.rating', 'r.review', 'r.review_date', 'r.appointment_id')
                ->where('r.status', 1)
                ->orderByDesc('r.is_popular')
                ->limit(6)
                ->get();

            $appointmentIds = $reviews->pluck('appointment_id')->filter()->unique()->toArray();
            if (!empty($appointmentIds)) {
                $appointments = DB::table('appointments')
                    ->whereIn('id', $appointmentIds)
                    ->pluck('service_id', 'id');

                $allServiceIds = [];
                foreach ($appointments as $sidString) {
                    if ($sidString) {
                        $allServiceIds = array_merge($allServiceIds, explode(',', $sidString));
                    }
                }
                $allServiceIds = array_unique($allServiceIds);

                $serviceNames = DB::table('service_masters')
                    ->whereIn('id', $allServiceIds)
                    ->pluck('name', 'id');

                $reviews->map(function ($review) use ($appointments, $serviceNames) {
                    $sids = isset($appointments[$review->appointment_id]) ? explode(',', $appointments[$review->appointment_id]) : [];
                    $review->services = array_map(fn($id) => $serviceNames[$id] ?? null, $sids);
                    $review->services = array_values(array_filter($review->services));
                    return $review;
                });
            } else {
                $reviews->map(function ($review) {
                    $review->services = [];
                    return $review;
                });
            }

            // 7. Service Combos (Optimized)
            $combos = DB::table('service_combos')
                ->select('id', 'name', 'description', DB::raw('CONCAT("' . asset('uploads/combos') . '/", image) AS image'), 'min_price')
                ->where('status', 1)
                ->get();

            $comboIds = $combos->pluck('id')->toArray();
            if (!empty($comboIds)) {
                $itemsQuery = DB::table('service_combo_items as sci')
                    ->join('service_masters as sm', 'sci.service_master_id', '=', 'sm.id');
                
                if ($cityId) {
                    $itemsQuery->leftJoin('service_city_masters as scm', function($join) use ($cityId) {
                        $join->on('scm.service_master_id', '=', 'sm.id')
                             ->where('scm.city_id', $cityId);
                    })
                    ->select(
                        'sci.combo_id',
                        'sm.id',
                        'sm.id as service_id',
                        'sm.name',
                        'sm.category_id',
                        'sm.sub_category_id',
                        DB::raw('IFNULL(scm.price, sm.price) as price'),
                        DB::raw('IFNULL(scm.discount_price, sm.discount_price) as discount_price'),
                        'sm.duration',
                        'sci.is_default'
                    );
                } else {
                    $itemsQuery->select(
                        'sci.combo_id',
                        'sm.id',
                        'sm.id as service_id',
                        'sm.name',
                        'sm.category_id',
                        'sm.sub_category_id',
                        'sm.price',
                        'sm.discount_price',
                        'sm.duration',
                        'sci.is_default'
                    );
                }
                
                $allItems = $itemsQuery->whereIn('sci.combo_id', $comboIds)->get()->groupBy('combo_id');
                
                $combos->each(function ($combo) use ($allItems) {
                    $combo->items = $allItems->get($combo->id, []);
                });
            }

            // 8. Others
            $productBrands = DB::table('product_brands')
                ->select('id', 'name', DB::raw('CONCAT("' . asset('uploads/product-brand') . '/", icon) AS icon'))
                ->where('status', 1)
                ->orderBy('name', 'ASC')
                ->get();

            $cities = DB::table('cities')
                ->select('id', 'name', 'status')
                ->whereIn('status', [0, 1])
                ->orderBy('name', 'asc')
                ->get();

            $activeCities = $cities->where('status', 0)->values();
            $comingSoonCities = $cities->where('status', 1)->values();

            $coupons = DB::table('coupon_codes')
                ->select('id', 'code', 'discount_type', 'discount_value', 'description', 'start_date', 'end_date')
                ->where('status', 1)
                ->where(function($query) {
                    $query->whereNull('start_date')->orWhere('start_date', '<=', now());
                })
                ->where(function($query) {
                    $query->whereNull('end_date')->orWhere('end_date', '>=', now());
                })
                ->get();

            $responseData = [
                'user' => $userData,
                'offers' => $offers,
                'coupons' => $coupons,
                'membership_plans' => $membershipPlans,
                'categories' => $categories,
                'search_results' => $services,
                'trending_services' => $trendingData,
                'reviews' => $reviews,
                'combos' => $combos,
                'brands' => $productBrands,
                'active_cities' => $activeCities,
                'coming_soon_cities' => $comingSoonCities,
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
