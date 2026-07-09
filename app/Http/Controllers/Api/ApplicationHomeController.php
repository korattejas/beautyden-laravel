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

        // 1. Validate: city_id is required
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'city_id' => 'required|integer',
            'city_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError(
                'City not found. Please select a city to continue.',
                $this->validation_error_status
            );
        }

        try {
            $cityId = $request->input('city_id');
            $cityName = $request->input('city_name', '');

            if ($cityId != 0) {
                // 2. Check City Status (Active vs Coming Soon)
                $city = DB::table('cities')->where('id', $cityId)->first();
                
                if (!$city) {
                    return $this->sendError('City not found.', 404);
                }

                // status 0 = Active, status 1 = Coming Soon
                if ($city->status != 0) {
                    return $this->sendResponse(
                        [
                            'is_coming_soon' => true,
                            'city_name' => $city->name,
                            'current_user_selected_city_name' => $cityName
                        ],
                        "Coming soon! We are not available in $city->name yet, but we will start our services here very soon.",
                        $this->success_status
                    );
                }
            }

            // 1. User Details & Subscription
            $user = auth('user')->user();
            $userData = null;
            if ($user) {
                $user->update([
                    'last_login_at' => now(),
                    'last_login_ip' => $request->ip()
                ]);

                $addresses = $user->addresses()
                    ->select(
                        'id',
                        'user_id',
                        DB::raw(($user->city_id ?? 'NULL') . ' as city_id'),
                        'address',
                        'latitude',
                        'longitude',
                        'type',
                        'is_default'
                    )
                    ->orderBy('is_default', 'desc')
                    ->orderBy('id', 'desc')
                    ->get();

                // Check active Elite subscription
                // $subscription = DB::table('user_subscriptions as us')
                //     ->join('membership_plans as mp', 'us.plan_id', '=', 'mp.id')
                //     ->where('us.user_id', $user->id)
                //     ->where('us.status', 1)
                //     ->where('us.end_date', '>=', now())
                //     ->select('us.id', 'us.plan_id', 'us.end_date', 'mp.name as plan_name')
                //     ->first();

                $userData = [
                    'id' => (int) $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'mobile_number' => $user->mobile_number,
                    'city_id' => (int) $user->city_id,
                    'address' => $user->address,
                    'active_address' => $addresses->where('is_default', 1)->first(),
                    'addresses' => $addresses,
                    // 'active_subscription' => $subscription
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
                    'link',
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

            // 3. Service Types (New Feature)
            $serviceTypes = \App\Models\ServiceType::where('status', 1)
                ->select('id', 'name', DB::raw('CONCAT("' . asset('uploads/service-types') . '/", icon) AS icon'), 'description', 'is_popular', 'is_new')
                ->orderByDesc('is_popular')
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

            if ($cityId == 0) {
                return $this->sendResponse(
                    [
                        'current_user_selected_city_name' => $cityName,
                        'offers' => $offers,
                        'coupons' => $coupons,
                        'service_types' => $serviceTypes,
                        'active_cities' => $activeCities,
                        'coming_soon_cities' => $comingSoonCities,
                    ],
                    'Home page data retrieved successfully',
                    $this->success_status
                );
            }


            // 4. Category List (Service Categories)
            $categories = DB::table('service_categories')
                ->select(
                    'id', 
                    'name', 
                    DB::raw('CONCAT("' . asset('uploads/service-category') . '/", icon) AS icon'), 
                    'description', 
                    'is_popular',
                    'is_new'
                )
                ->where('status', 1)
                ->where('is_popular', 1)
                ->orderByDesc('is_popular')
                ->get();

            // All active categories map for trending lookup (not filtered by is_popular)
            $allCategoriesMap = DB::table('service_categories')
                ->select('id', 'name')
                ->where('status', 1)
                ->get()
                ->keyBy('id');

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
                    'scm.price as price', 
                    DB::raw('ROUND(scm.price + (scm.price * scm.discount_price / 100)) as discount_price'),
                    'scm.discount_price as discount_percentage',
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
                    's.price as price', 
                    's.discount_price as discount_price',
                    DB::raw('IF(s.discount_price > s.price, ROUND(((s.discount_price - s.price) / s.discount_price) * 100), 0) as discount_percentage'),
                    's.duration', 
                    's.rating', 
                    's.reviews', 
                    DB::raw('CONCAT("' . asset('uploads/service') . '/", s.icon) AS icon')
                );
            }



            // Group trending services by category for tabs
            $trendingServices = (clone $servicesQuery)
                ->where('s.is_popular', 1)
                ->get()
                ->groupBy('category_id');

            $trendingData = [];
            foreach ($trendingServices as $catId => $items) {
                // Use allCategoriesMap so trending services are not limited to is_popular categories
                $category = $allCategoriesMap->get($catId);
                if ($category) {
                    $items->transform(function ($item) {
                        $item->price = (int) $item->price;
                        $item->discount_price = (int) $item->discount_price;
                        $item->discount_percentage = (int) $item->discount_percentage;
                        return $item;
                    });
                    
                    $trendingData[] = [
                        'category_id' => $catId,
                        'category_name' => $category->name,
                        'services' => $items
                    ];
                }
            }

            // // 6. Reviews (Optimized)
            // $reviews = DB::table('customer_reviews as r')
            //     ->select('r.id', 'r.customer_name', 'r.rating', 'r.review', 'r.review_date', 'r.appointment_id')
            //     ->where('r.status', 1)
            //     ->orderByDesc('r.is_popular')
            //     ->limit(10)
            //     ->get();

            // $appointmentIds = $reviews->pluck('appointment_id')->filter()->unique()->toArray();
            // if (!empty($appointmentIds)) {
            //     $appointments = DB::table('appointments')
            //         ->whereIn('id', $appointmentIds)
            //         ->pluck('service_id', 'id');

            //     $allServiceIds = [];
            //     foreach ($appointments as $sidString) {
            //         if ($sidString) {
            //             $allServiceIds = array_merge($allServiceIds, explode(',', $sidString));
            //         }
            //     }
            //     $allServiceIds = array_unique($allServiceIds);

            //     $serviceNames = DB::table('service_masters')
            //         ->whereIn('id', $allServiceIds)
            //         ->pluck('name', 'id');

            //     $reviews->map(function ($review) use ($appointments, $serviceNames) {
            //         $sids = isset($appointments[$review->appointment_id]) ? explode(',', $appointments[$review->appointment_id]) : [];
            //         $review->services = array_map(fn($id) => $serviceNames[$id] ?? null, $sids);
            //         $review->services = array_values(array_filter($review->services));
            //         return $review;
            //     });
            // } else {
            //     $reviews->map(function ($review) {
            //         $review->services = [];
            //         return $review;
            //     });
            // }

            // // 8. Others
            // $productBrands = DB::table('product_brands')
            //     ->select('id', 'name', DB::raw('CONCAT("' . asset('uploads/product-brand') . '/", icon) AS icon'))
            //     ->where('status', 1)
            //     ->orderBy('name', 'ASC')
            //     ->get();
            $responseData = [
                'current_user_selected_city_name' => $cityName,
                'user' => $userData,
                'offers' => $offers,
                'coupons' => $coupons,
                'service_types' => $serviceTypes,
                'categories' => $categories,

                'trending_services' => $trendingData,
                // 'reviews' => $reviews,
                // 'brands' => $productBrands,
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

    public function getServiceCombos(Request $request): JsonResponse
    {
        $function_name = 'getServiceCombos';

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'city_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError(
                'Invalid parameters.',
                $this->validation_error_status
            );
        }

        try {
            $cityId = $request->input('city_id');
            
            if ($cityId == 0) {
                return $this->sendResponse(
                    [
                        'is_coming_soon' => true,
                        'combos' => [],
                    ],
                    "Explore our app while we prepare to launch in your city!",
                    $this->success_status
                );
            }

            // Check City Status (Active vs Coming Soon)
            $city = DB::table('cities')->where('id', $cityId)->first();
            if (!$city) {
                return $this->sendError('City not found.', 404);
            }
            if ($city->status != 0) {
                return $this->sendResponse(
                    [
                        'is_coming_soon' => true,
                        'city_name' => $city->name,
                        'combos' => [],
                    ],
                    "Coming soon! We are not available in $city->name yet, but we will start our services here very soon.",
                    $this->success_status
                );
            }

            $combos = DB::table('service_combos')
                ->select('id', 'name', 'description', DB::raw('CONCAT("' . asset('uploads/combos') . '/", image) AS image'))
                ->where('status', 1)
                ->get();

            $comboIds = $combos->pluck('id')->toArray();
            if (!empty($comboIds)) {
                $itemsQuery = DB::table('service_combo_items as sci')
                    ->join('service_masters as sm', 'sci.service_master_id', '=', 'sm.id');
                
                $itemsQuery->leftJoin('service_city_masters as scm', function($join) use ($cityId) {
                    $join->on('scm.service_master_id', '=', 'sm.id')
                         ->where('scm.city_id', $cityId);
                })
                ->leftJoin('service_master_variants as smv', 'sci.variant_id', '=', 'smv.id')
                ->leftJoin('service_city_variant_prices as scvp', function($join) use ($cityId) {
                    $join->on('scvp.variant_id', '=', 'sci.variant_id')
                         ->where('scvp.city_id', $cityId);
                })
                ->leftJoin('service_categories as sc', 'sm.category_id', '=', 'sc.id')
                ->select(
                    'sci.combo_id',
                    'sm.id',
                    'sm.id as service_id',
                    DB::raw('IF(sci.variant_id IS NOT NULL, CONCAT(sm.name, " - ", smv.name), sm.name) as name'),
                    'sm.description',
                    'sm.category_id',
                    'sc.name as category_name',
                    'sm.sub_category_id',
                    DB::raw('IF(sci.variant_id IS NOT NULL, IFNULL(scvp.price, smv.price), IFNULL(scm.price, sm.price)) as price'),
                    DB::raw('IF(
                        sci.variant_id IS NOT NULL, 
                        ROUND(IFNULL(scvp.price, smv.price) + (IFNULL(scvp.price, smv.price) * IFNULL(scvp.discount_price, smv.discount_percentage) / 100), 2), 
                        IF(scm.id IS NOT NULL, ROUND(scm.price + (scm.price * scm.discount_price / 100), 2), sm.discount_price)
                    ) as discount_price'),
                    DB::raw('IF(
                        sci.variant_id IS NOT NULL, 
                        IFNULL(scvp.discount_price, smv.discount_percentage), 
                        IF(scm.id IS NOT NULL, scm.discount_price, IF(sm.discount_price > sm.price, ROUND(((sm.discount_price - sm.price) / sm.discount_price) * 100), 0))
                    ) as discount_percentage'),
                    DB::raw('IF(sci.variant_id IS NOT NULL, smv.duration, sm.duration) as duration'),
                    'sci.is_default',
                    'sci.variant_id'
                );
                
                $allItems = $itemsQuery->whereIn('sci.combo_id', $comboIds)->get()->groupBy('combo_id');
                
                $combos->each(function ($combo) use ($allItems) {
                    $items = $allItems->get($combo->id, collect([]));
                    
                    $items->transform(function ($item) {
                        $item->price = (int) $item->price;
                        $item->discount_price = (int) $item->discount_price;
                        $item->discount_percentage = (int) $item->discount_percentage;
                        return $item;
                    });
                    
                    $combo->items = $items;
                    
                    $totalDuration = $items->where('is_default', 1)->sum(function ($item) {
                        return (int) $item->duration;
                    });
                    
                    $combo->total_duration = $totalDuration;
                    
                    if ($totalDuration >= 60) {
                        $hours = floor($totalDuration / 60);
                        $minutes = $totalDuration % 60;
                        if ($minutes > 0) {
                            $combo->total_duration_formatted = $hours . ' hr ' . $minutes . ' min.';
                        } else {
                            $combo->total_duration_formatted = $hours . ' hr.';
                        }
                    } else {
                        $combo->total_duration_formatted = $totalDuration . ' min.';
                    }
                    $combo->total_price = (int) $items->where('is_default', 1)->sum(function ($item) {
                        return $item->price;
                    });
                    $combo->total_discount_price = (int) $items->where('is_default', 1)->sum(function ($item) {
                        return $item->discount_price;
                    });
                    
                    $combo->discount_percentage = (int) round($items->where('is_default', 1)->avg(function ($item) {
                        return $item->discount_percentage;
                    }));
                });
            }

            return $this->sendResponse(
                ['combos' => $combos],
                'Service combos retrieved successfully',
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

    // ═══════════════════════════════════════════════════════════════════════
    //  Search / Browse API
    //  GET params (all optional):
    //    city_id         – price from service_city_masters when set
    //    search          – free-text → returns matching services
    //    category_id     – returns sub-categories of that category
    //    sub_category_id – returns services of that sub-category
    //  Default (no param)  → returns all active categories
    // ═══════════════════════════════════════════════════════════════════════
    public function getServiceSearchData(Request $request): JsonResponse
    {
        $function_name = 'getServiceSearchData';

        // 1. Validate: city_id is required
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'city_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError(
                'City not found. Please select a city to continue.',
                $this->validation_error_status
            );
        }

        try {
            $cityId        = $request->input('city_id');
            $categoryId    = $request->input('category_id');
            $subCategoryId = $request->input('sub_category_id');
            $search        = trim($request->input('search', ''));

            // 2. Check City Status
            $city = DB::table('cities')->where('id', $cityId)->first();
            if (!$city) {
                return $this->sendError('City not found.', 404);
            }
            if ($city->status != 0) {
                return $this->sendResponse(['is_coming_soon' => true, 'city_name' => $city->name], "Coming soon in $city->name!", $this->success_status);
            }

            // 3. Always fetch All Categories (Status 1) for the top navigation/filter
            $allCategories = DB::table('service_categories')
                ->select('id', 'name', DB::raw('CONCAT("' . asset('uploads/service-category') . '/", icon) AS icon'), 'is_new')
                ->where('status', 1)
                ->orderBy('name')
                ->get();

            // 4. Helper: Services Query
            $buildServicesQuery = function (\Illuminate\Database\Query\Builder $q) use ($cityId) {
                $q->join('service_city_masters as scm', function ($join) use ($cityId) {
                    $join->on('scm.service_master_id', '=', 's.id')
                         ->where('scm.city_id', '=', $cityId)
                         ->where('scm.status', '=', 1);
                })
                ->select(
                    's.id', 's.name', 's.category_id', 's.sub_category_id',
                    'scm.price as base_price', 'scm.discount_price',
                    's.duration', 's.rating', 's.reviews', 's.description',
                    DB::raw('CONCAT("' . asset('uploads/service') . '/", s.icon) AS icon')
                );
                return $q;
            };

            $responseType = 'categories';
            $responseData = $allCategories;

            // 5. Logic based on input
            if ($search !== '') {
                $q = DB::table('service_masters as s')->where('s.status', 1)
                    ->where(function ($q) use ($search) {
                        $q->where('s.name', 'like', "%$search%")->orWhere('s.description', 'like', "%$search%");
                    });
                $buildServicesQuery($q);
                $responseData = $q->get();
                $responseType = 'services';
            } 
            elseif ($subCategoryId) {
                $q = DB::table('service_masters as s')->where('s.status', 1)->where('s.sub_category_id', $subCategoryId);
                $buildServicesQuery($q);
                $responseData = $q->get();
                $responseType = 'services';
            } 
            elseif ($categoryId) {
                $responseData = DB::table('service_subcategories')
                    ->select('id', 'name', 'service_category_id as category_id', DB::raw('CONCAT("' . asset('uploads/service-subcategory') . '/", icon) AS icon'))
                    ->where('service_category_id', $categoryId)->where('status', 1)->orderBy('name')->get();
                $responseType = 'subcategories';
            }

            return $this->sendResponse(
                [
                    'categories' => $allCategories, // Always include categories for UI navigation
                    'type'       => $responseType,
                    'results'    => $responseData
                ],
                'Data retrieved successfully.',
                $this->success_status
            );

        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }
}
