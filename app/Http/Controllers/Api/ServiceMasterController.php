<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceMaster;
use App\Models\ServiceCityMaster;
use App\Models\ServiceEssential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Exception;

class ServiceMasterController extends Controller
{
    protected mixed $success_status, $exception_status, $backend_error_status, $validation_error_status, $common_error_message;

    protected string $controller_name;

    public function __construct()
    {
        $this->controller_name = 'API/ServiceMasterController';
        $this->success_status = config('custom.status_code_for_success');
        $this->exception_status = config('custom.status_code_for_exception_error');
        $this->backend_error_status = config('custom.status_code_for_backend_error');
        $this->validation_error_status = config('custom.status_code_for_validation_error');
        $this->common_error_message = config('custom.common_error_message');
    }

    private function getCategoryReviewStats($categoryId)
    {
        // $dummyReviewsSum = DB::table('service_masters')->where('category_id', $categoryId)->sum('reviews');
        // $dummyRatingAvg = DB::table('service_masters')->where('category_id', $categoryId)->where('rating', '>', 0)->avg('rating');
        
        $realReviewsCount = DB::table('customer_reviews')->where('category_id', $categoryId)->where('status', 1)->count();
        $realRatingAvg = DB::table('customer_reviews')->where('category_id', $categoryId)->where('status', 1)->avg('rating');

        // $totalReviews = $dummyReviewsSum + $realReviewsCount;
        $totalReviews = $realReviewsCount;
        
        $totalRatingPoints = 0;
        $pointsCount = 0;
        
        // if ($dummyReviewsSum > 0 && $dummyRatingAvg > 0) {
        //     $totalRatingPoints += ($dummyRatingAvg * $dummyReviewsSum);
        //     $pointsCount += $dummyReviewsSum;
        // }
        
        if ($realReviewsCount > 0 && $realRatingAvg > 0) {
            $totalRatingPoints += ($realRatingAvg * $realReviewsCount);
            $pointsCount += $realReviewsCount;
        }
        
        $finalRating = $pointsCount > 0 ? round($totalRatingPoints / $pointsCount, 1) : 0;
        
        return [
            'reviews' => (int) $totalReviews,
            'rating' => (float) $finalRating
        ];
    }

    public function getServiceMasters(Request $request): JsonResponse
    {
        $function_name = 'getServiceMasters';

        try {
            $cityId = $request->city_id;

            if (!$cityId) {
                return $this->sendError('City ID is required.', $this->validation_error_status);
            }

            $query = DB::table('service_city_masters as scm')
                ->join('service_masters as sm', 'scm.service_master_id', '=', 'sm.id')
                ->join('service_categories as c', 'sm.category_id', '=', 'c.id')
                ->leftJoin('service_subcategories as csc', 'sm.sub_category_id', '=', 'csc.id')
                ->select(
                    'sm.id',
                    'sm.category_id',
                    'sm.sub_category_id',
                    'c.name as category_name',
                    'csc.name as sub_category_name',
                    'sm.name',
                    'sm.skin_type',
                    'scm.price as price',
                    DB::raw('ROUND(scm.price + (scm.price * scm.discount_price / 100)) as discount_price'),
                    'scm.discount_price as discount_percentage',
                    'sm.duration',
                    'sm.rating',
                    'sm.reviews',
                    'sm.description',
                    DB::raw('CONCAT("' . asset('uploads/service') . '/", sm.icon) AS icon'),
                    DB::raw('IF(sm.icon LIKE "%.mp4" OR sm.icon LIKE "%.mov" OR sm.icon LIKE "%.avi" OR sm.icon LIKE "%.wmv", "video", "image") AS icon_type'),
                    'sm.is_popular',
                    'sm.has_variants',
                    'sm.banner_media'
                )
                ->where('scm.status', 1)
                ->where('sm.status', 1);

            $query->where('scm.city_id', $cityId);

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('sm.name', 'like', "%$search%")
                        ->orWhere('sm.description', 'like', "%$search%")
                        ->orWhere('c.name', 'like', "%$search%")
                        ->orWhere('sm.duration', 'like', "%$search%")
                        ->orWhere('sm.skin_type', 'like', "%$search%");
                });
            }

            if ($request->filled('category_id')) {
                $query->where('sm.category_id', $request->category_id);
            }

            if ($request->filled('sub_category_id')) {
                $query->where('sm.sub_category_id', $request->sub_category_id);
            }

            if ($request->filled('skin_type')) {
                $query->where('sm.skin_type', $request->skin_type);
            }

            if ($request->filled('min_price')) {
                $query->where('scm.price', '>=', $request->min_price);
            }

            if ($request->filled('max_price')) {
                $query->where('scm.price', '<=', $request->max_price);
            }

            $perPage = $request->per_page ?? 24;
            $page = $request->page ?? 1;

            $services = $query->orderByDesc('sm.is_popular')
                ->paginate($perPage, ['*'], 'page', $page);
                
            $categoryStatsCache = [];

            // For variant services: only load city prices to compute starts_at & total_option.
            // Full variant details (name, description, thumbnail, etc.) are NOT needed here —
            // they are fetched on-demand via getServiceVariantDetails when user taps "View Option".
            $serviceIdsWithVariants = collect($services->items())->filter(function ($service) {
                return $service->has_variants == 1;
            })->pluck('id')->toArray();

            $variantPrices = collect();

            if (!empty($serviceIdsWithVariants)) {
                $variantPrices = \App\Models\ServiceCityVariantPrice::whereIn('service_master_id', $serviceIdsWithVariants)
                    ->where('city_id', $cityId)
                    ->where('is_available', 1)
                    ->get()
                    ->groupBy('service_master_id');
            }

            $services->getCollection()->transform(function ($service) use (&$categoryStatsCache, $variantPrices) {
                $service->price = (int) $service->price;
                $service->discount_price = (int) $service->discount_price;
                $service->discount_percentage = (int) $service->discount_percentage;

                $service->is_popular = (int) $service->is_popular;
                $service->has_variants = (int) $service->has_variants;

                if (!isset($categoryStatsCache[$service->category_id])) {
                    $categoryStatsCache[$service->category_id] = $this->getCategoryReviewStats($service->category_id);
                }

                $service->rating = (string) $categoryStatsCache[$service->category_id]['rating'];
                $service->reviews = (string) $categoryStatsCache[$service->category_id]['reviews'];

                $banner_media = $service->banner_media ? json_decode($service->banner_media, true) : [];
                $formatted_banner = [];

                if (!empty($banner_media) && count($banner_media) > 0) {
                    $formatted_banner = collect($banner_media)->map(function ($media) {
                        $media['url'] = asset('uploads/service-media/' . $media['url']);
                        if (!isset($media['type'])) {
                            $ext = strtolower(pathinfo($media['url'], PATHINFO_EXTENSION));
                            $media['type'] = in_array($ext, ['mp4', 'mov', 'avi', 'wmv']) ? 'video' : 'image';
                        }
                        $media['is_scroll_banner_image'] = isset($media['is_scroll_banner_image']) ? (int) $media['is_scroll_banner_image'] : 0;
                        return $media;
                    })->toArray();
                }

                $service->banner_section = empty($formatted_banner) ? null : [
                    'type' => 'banner_section',
                    'data' => array_values($formatted_banner)
                ];

                unset($service->banner_media);

                if ($service->has_variants == 1) {
                    $cityPrices = $variantPrices->get($service->id, collect());

                    if ($cityPrices->isNotEmpty()) {
                        $service->starts_at   = (int) $cityPrices->min('price');
                        $service->total_option = $cityPrices->count();
                    } else {
                        // Fallback: If no city prices exist (or IDs are mismatched), use the default prices from variants table
                        $dbVariants = \App\Models\ServiceMasterVariant::where('service_master_id', $service->id)->get();
                        if ($dbVariants->isNotEmpty()) {
                            $service->starts_at   = (int) $dbVariants->min('price');
                            $service->total_option = $dbVariants->count();
                        } else {
                            $service->has_variants = 0;
                        }
                    }

                    // Remove per-service price fields; card only shows starts_at
                    unset($service->price, $service->discount_price, $service->discount_percentage, $service->duration);
                }

                return $service;
            });

            if ($services->total() === 0) {
                return $this->sendError('No service found.', $this->backend_error_status);
            }

            return $this->sendResponse(
                $services,
                'Services retrieved successfully',
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

    public function getServiceMasterDetails(Request $request): JsonResponse
    {
        $function_name = 'getServiceMasterDetails';

        try {
            $serviceId = $request->service_id;
            $cityId = $request->city_id;
            $requestedVariantId = $request->variant_id; // Optional variant ID

            if (!$serviceId || !$cityId) {
                return $this->sendError('Service ID and City ID are required.', $this->validation_error_status);
            }

            $service = ServiceMaster::with(['category', 'subcategory', 'variants'])
                ->where('id', $serviceId)
                ->where('status', 1)
                ->first();

            if (!$service) {
                return $this->sendError('Service not found.', $this->backend_error_status);
            }

            $service->category_name = $service->category->name ?? '';
            $service->sub_category_name = $service->subcategory->name ?? '';

            // Override with Category-wise ratings
            $catStats = $this->getCategoryReviewStats($service->category_id);
            $service->rating = (string) $catStats['rating'];
            $service->reviews = (string) $catStats['reviews'];

            if ($service->subcategory && $service->subcategory->media_json) {
                $media = $service->subcategory->media_json;
                $media['images'] = array_map(fn($img) => asset('uploads/service-media/' . $img), $media['images'] ?? []);
                $media['videos'] = array_map(fn($vid) => asset('uploads/service-media/' . $vid), $media['videos'] ?? []);
                $service->subcategory->media_json = $media;
            }

            // Get specific price for city if provided
            if ($cityId) {
                $cityService = ServiceCityMaster::where('service_master_id', $serviceId)
                    ->where('city_id', $cityId)
                    ->where('status', 1)
                    ->first();
                
                if ($cityService) {
                    $service->price = (int) $cityService->price;
                    $service->discount_price = (int) round($cityService->price + ($cityService->price * $cityService->discount_price / 100));
                    $service->discount_percentage = (int) $cityService->discount_price;
                }

                if ($service->has_variants == 1 && $service->variants) {
                    $variantPrices = \App\Models\ServiceCityVariantPrice::where('service_master_id', $serviceId)
                        ->where('city_id', $cityId)
                        ->get()->keyBy('variant_id');
                    
                    $availableVariants = [];
                    $selectedVariant = null;

                    foreach ($service->variants as $variant) {
                        if (isset($variantPrices[$variant->id])) {
                            // Check if variant is available for this city
                            if ($variantPrices[$variant->id]->is_available == 0) {
                                continue; // Skip this variant
                            }
                            $priceData = $variantPrices[$variant->id];
                            $variant->price = (int) $priceData->price;
                            $variant->discount_price = (int) round($priceData->price + ($priceData->price * $priceData->discount_price / 100));
                            $variant->discount_percentage = (int) $priceData->discount_price;
                        } else {
                            // Fallback to default price
                            $variant->price = (int) $variant->price;
                            $variant->discount_price = (int) round($variant->price + ($variant->price * $variant->discount_percentage / 100));
                            $variant->discount_percentage = (int) $variant->discount_percentage;
                        }

                        $rawThumbnail = $variant->thumbnail_image;

                        // Format thumbnail image URL
                        $variant->thumbnail_image = $variant->thumbnail_image
                            ? asset('uploads/service-variant/' . $variant->thumbnail_image)
                            : null;

                        // Ensure numeric types
                        $variant->description         = $variant->description ?? null;
                        $variant->rating             = (string) $catStats['rating'];
                        $variant->reviews            = (string) $catStats['reviews'];

                        $availableVariants[] = $variant;

                        if ($requestedVariantId && $variant->id == $requestedVariantId) {
                            $selectedVariant = clone $variant;
                            $selectedVariant->raw_thumbnail = $rawThumbnail;
                        }
                    }
                    
                    if ($requestedVariantId) {
                        if ($selectedVariant) {
                            // Override main service details with the variant's details
                            $service->name = $selectedVariant->name;
                            $service->price = $selectedVariant->price;
                            $service->discount_price = $selectedVariant->discount_price;
                            $service->discount_percentage = $selectedVariant->discount_percentage;
                            $service->duration = $selectedVariant->duration;
                            if ($selectedVariant->description) {
                                $service->description = $selectedVariant->description;
                            }
                            if ($selectedVariant->raw_thumbnail) {
                                $service->icon = $selectedVariant->raw_thumbnail;
                                $service->is_variant_icon = true;
                            }
                        }
                        unset($service->variants);
                        $service->has_variants = 0;
                    } else {
                        $service->variants = $availableVariants;
                    }
                }
            }

            if ($service->has_variants == 0 || empty($service->variants)) {
                unset($service->variants);
                $service->has_variants = 0;
            }

            // Format image/media URLs
            $ext = strtolower(pathinfo($service->icon, PATHINFO_EXTENSION));
            $service->icon_type = in_array($ext, ['mp4', 'mov', 'avi', 'wmv']) ? 'video' : 'image';
            if (isset($service->is_variant_icon) && $service->is_variant_icon) {
                $service->icon = asset('uploads/service-variant/' . $service->icon);
            } else {
                $service->icon = asset('uploads/service/' . $service->icon);
            }
            
            if ($service->banner_media) {
                $service->banner_media = collect($service->banner_media)->map(function ($media) {
                    $media['url'] = asset('uploads/service-media/' . $media['url']);
                    if (!isset($media['type'])) {
                        $ext = strtolower(pathinfo($media['url'], PATHINFO_EXTENSION));
                        $media['type'] = in_array($ext, ['mp4', 'mov', 'avi', 'wmv']) ? 'video' : 'image';
                    }
                    $media['is_scroll_banner_image'] = isset($media['is_scroll_banner_image']) ? (int) $media['is_scroll_banner_image'] : 0;
                    return $media;
                });
            }

            if ($service->before_after) {
                $service->before_after = collect($service->before_after)->map(function ($image) {
                    $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
                    return [
                        'url' => asset('uploads/service-media/' . $image),
                        'type' => in_array($ext, ['mp4', 'mov', 'avi', 'wmv']) ? 'video' : 'image'
                    ];
                });
            }

            // content_json images
            if ($service->content_json) {
                $sections = $service->content_json;
                foreach ($sections as &$section) {
                    if (isset($section['steps'])) {
                        foreach ($section['steps'] as &$step) {
                            if (isset($step['image']) && !empty($step['image'])) {
                                $ext = strtolower(pathinfo($step['image'], PATHINFO_EXTENSION));
                                $step['image_type'] = in_array($ext, ['mp4', 'mov', 'avi', 'wmv']) ? 'video' : 'image';
                                $step['image'] = asset('uploads/service-content/' . $step['image']);
                            }
                        }
                    }
                    if (isset($section['image']) && !empty($section['image'])) {
                        $ext = strtolower(pathinfo($section['image'], PATHINFO_EXTENSION));
                        $section['image_type'] = in_array($ext, ['mp4', 'mov', 'avi', 'wmv']) ? 'video' : 'image';
                        $section['image'] = asset('uploads/service-content/' . $section['image']);
                    }
                    if (isset($section['items'])) {
                        foreach ($section['items'] as &$item) {
                            if (isset($item['image']) && !empty($item['image'])) {
                                $ext = strtolower(pathinfo($item['image'], PATHINFO_EXTENSION));
                                $item['image_type'] = in_array($ext, ['mp4', 'mov', 'avi', 'wmv']) ? 'video' : 'image';
                                $item['image'] = asset('uploads/service-content/' . $item['image']);
                            }
                        }
                    }

                    if (isset($section['type']) && $section['type'] === 'overview' && isset($section['essential_ids'])) {
                        $essentialIds = $section['essential_ids'];
                        $section['essentials'] = ServiceEssential::whereIn('id', (array)$essentialIds)
                            ->where('status', 1)
                            ->get()
                            ->map(function ($essential) {
                                return [
                                    'id' => $essential->id,
                                    'title' => $essential->title,
                                    'type' => $essential->type,
                                    'icon' => $essential->icon ? asset('uploads/essential/' . $essential->icon) : null,
                                ];
                            });
                    }
                }
                $service->content_json = $sections;
            }

            $service->is_popular = (int) $service->is_popular;
            $service->has_variants = (int) $service->has_variants;
            $service->status = (int) $service->status;

            // Get related popular services in the same category
            $relatedServices = DB::table('service_city_masters as scm')
                ->join('service_masters as sm', 'scm.service_master_id', '=', 'sm.id')
                ->select(
                    'sm.id',
                    'sm.name',
                    'scm.price as price',
                    DB::raw('ROUND(scm.price + (scm.price * scm.discount_price / 100)) as discount_price'),
                    'scm.discount_price as discount_percentage',
                    'sm.duration',
                    'sm.rating',
                    'sm.reviews',
                    DB::raw('CONCAT("' . asset('uploads/service') . '/", sm.icon) AS icon'),
                    DB::raw('IF(sm.icon LIKE "%.mp4" OR sm.icon LIKE "%.mov" OR sm.icon LIKE "%.avi" OR sm.icon LIKE "%.wmv", "video", "image") AS icon_type'),
                    'sm.is_popular',
                    'sm.has_variants'
                )
                ->where('sm.category_id', $service->category_id)
                ->where('sm.id', '!=', $serviceId)
                ->where('sm.is_popular', 1)
                ->where('sm.status', 1)
                ->where('scm.city_id', $cityId)
                ->where('scm.status', 1)
                ->get();

            $relatedServiceIdsWithVariants = $relatedServices->where('has_variants', 1)->pluck('id')->toArray();
            $relVariantPrices = collect();

            if (!empty($relatedServiceIdsWithVariants)) {
                $relVariantPrices = \App\Models\ServiceCityVariantPrice::whereIn('service_master_id', $relatedServiceIdsWithVariants)
                    ->where('city_id', $cityId)
                    ->where('is_available', 1)
                    ->get()
                    ->groupBy('service_master_id');
            }

            $relatedServices = $relatedServices->map(function ($item) use ($catStats, $relVariantPrices) {
                $item->is_popular = (int) $item->is_popular;
                $item->rating = (string) $catStats['rating'];
                $item->reviews = (string) $catStats['reviews'];
                $item->has_variants = (int) $item->has_variants;
                
                if ($item->has_variants == 1) {
                    $cityPrices = $relVariantPrices->get($item->id, collect());

                    if ($cityPrices->isNotEmpty()) {
                        $item->starts_at = (int) $cityPrices->min('price');
                        $item->total_option = $cityPrices->count();
                    } else {
                        // Fallback to db
                        $dbVariants = \App\Models\ServiceMasterVariant::where('service_master_id', $item->id)->get();
                        if ($dbVariants->isNotEmpty()) {
                            $item->starts_at = (int) $dbVariants->min('price');
                            $item->total_option = $dbVariants->count();
                        } else {
                            $item->has_variants = 0;
                        }
                    }
                    
                    if ($item->has_variants == 1) {
                        unset($item->price, $item->discount_price, $item->discount_percentage, $item->duration);
                    } else {
                        // This handles the case where has_variants was set to 0 during fallback
                        $item->price = (int) $item->price;
                        $item->discount_price = (int) $item->discount_price;
                        $item->discount_percentage = (int) $item->discount_percentage;
                    }
                } else {
                    $item->price = (int) $item->price;
                    $item->discount_price = (int) $item->discount_price;
                    $item->discount_percentage = (int) $item->discount_percentage;
                }
                return $item;
            });

            $pageLayout = [];

            $serviceInfoData = [
                'id' => $service->id,
                'name' => $service->name,
                'rating' => $service->rating,
                'reviews' => $service->reviews,
                'icon' => $service->icon,
                'icon_type' => $service->icon_type,
                'category_name' => $service->category_name,
                'sub_category_name' => $service->sub_category_name,
                'is_popular' => $service->is_popular,
                'status' => $service->status,
                'has_variants' => $service->has_variants,
            ];

            if ($service->has_variants == 1 && !empty($service->variants)) {
                $serviceInfoData['starts_at'] = collect($service->variants)->min('price') ?? 0;
                $serviceInfoData['total_option'] = count($service->variants);
                $serviceInfoData['variants'] = $service->variants;
            } else {
                $serviceInfoData['price'] = $service->price ?? 0;
                $serviceInfoData['discount_price'] = $service->discount_price ?? 0;
                $serviceInfoData['discount_percentage'] = $service->discount_percentage ?? 0;
                $serviceInfoData['duration'] = $service->duration;
            }

            $pageLayout[] = [
                'type' => 'service_info_section',
                'data' => $serviceInfoData
            ];

            // 2. Filter Section (Currently for Facial)
            if (strtolower($service->category_name) == 'facial') {
                // Optimized: Fetch both names and skin_types in a single query
                $categoryServices = \App\Models\ServiceMaster::where('category_id', $service->category_id)
                    ->where('status', 1)
                    ->select('name', 'skin_type')
                    ->get();

                $skinTypes = $categoryServices->pluck('skin_type')
                    ->filter() // Removes nulls and empty strings
                    ->unique()
                    ->values()
                    ->toArray();
                array_unshift($skinTypes, 'All');

                $serviceNames = $categoryServices->pluck('name')->toArray();

                $subCategories = \App\Models\ServiceSubcategory::where('service_category_id', $service->category_id)
                    ->where('status', 1)
                    ->pluck('name')
                    ->toArray();
                array_unshift($subCategories, 'All');

                $pageLayout[] = [
                    'type' => 'filter_section',
                    'data' => [
                        'skin_types' => $skinTypes,
                        'price_filters' => [
                            ['label' => 'Low to High', 'value' => 'low_to_high'],
                            ['label' => 'High to Low', 'value' => 'high_to_low']
                        ],
                        'categories' => $subCategories,
                        'service_names' => $serviceNames
                    ]
                ];
            }

            // 3. Banner Section
            if (!empty($service->banner_media) && count($service->banner_media) > 0) {
                $pageLayout[] = [
                    'type' => 'banner_section',
                    'data' => $service->banner_media
                ];
            }

            // 4. Content JSON Sections
            if (!empty($service->content_json)) {
                foreach ($service->content_json as $contentSection) {
                    $pageLayout[] = $contentSection;
                }
            }

            // 5. Before & After Images
            if (!empty($service->before_after) && count($service->before_after) > 0) {
                $pageLayout[] = [
                    'type' => 'before_after_section',
                    'data' => $service->before_after
                ];
            }

            // 6. Related Services
            if ($relatedServices->count() > 0) {
                $pageLayout[] = [
                    'type' => 'related_services_section',
                    'data' => $relatedServices
                ];
            }



            return $this->sendResponse(
                ['page_layout' => $pageLayout],
                'Service details retrieved successfully',
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

    public function getServiceMasterDetailsOld(Request $request): JsonResponse
    {
        $function_name = 'getServiceMasterDetails';

        try {
            $serviceId = $request->service_id;
            $cityId = $request->city_id;

            if (!$serviceId || !$cityId) {
                return $this->sendError('Service ID and City ID are required.', $this->validation_error_status);
            }

            $service = ServiceMaster::with(['category', 'subcategory', 'variants'])
                ->where('id', $serviceId)
                ->where('status', 1)
                ->first();

            if (!$service) {
                return $this->sendError('Service not found.', $this->backend_error_status);
            }

            $service->category_name = $service->category->name ?? '';
            $service->sub_category_name = $service->subcategory->name ?? '';

            if ($service->subcategory && $service->subcategory->media_json) {
                $media = $service->subcategory->media_json;
                $media['images'] = array_map(fn($img) => asset('uploads/service-media/' . $img), $media['images'] ?? []);
                $media['videos'] = array_map(fn($vid) => asset('uploads/service-media/' . $vid), $media['videos'] ?? []);
                $service->subcategory->media_json = $media;
            }

            // Get specific price for city if provided
            if ($cityId) {
                $cityService = ServiceCityMaster::where('service_master_id', $serviceId)
                    ->where('city_id', $cityId)
                    ->where('status', 1)
                    ->first();
                
                if ($cityService) {
                    $service->price = (int) $cityService->price;
                    $service->discount_price = (int) round($cityService->price + ($cityService->price * $cityService->discount_price / 100));
                    $service->discount_percentage = (int) $cityService->discount_price;
                } else {
                    $service->discount_percentage = $service->discount_price > $service->price ? 
                        (int) round((($service->discount_price - $service->price) / $service->discount_price) * 100) : 0;
                    $service->price = (int) $service->price;
                    $service->discount_price = (int) $service->discount_price;
                }

                if ($service->has_variants == 1 && $service->variants) {
                    $variantPrices = \App\Models\ServiceCityVariantPrice::where('service_master_id', $serviceId)
                        ->where('city_id', $cityId)
                        ->get()->keyBy('variant_id');
                    
                    $availableVariants = [];
                    foreach ($service->variants as $variant) {
                        if (isset($variantPrices[$variant->id])) {
                            // Check if variant is available for this city
                            if ($variantPrices[$variant->id]->is_available == 0) {
                                continue; // Skip this variant
                            }
                            $variant->price = $variantPrices[$variant->id]->price;
                            $variant->discount_price = $variantPrices[$variant->id]->discount_price;
                        } else {
                            $variant->discount_price = 0; // Default if not found
                        }

                        // Format thumbnail image URL
                        $variant->thumbnail_image = $variant->thumbnail_image
                            ? asset('uploads/service-variant/' . $variant->thumbnail_image)
                            : null;

                        // Ensure numeric types
                        $variant->description         = $variant->description ?? null;
                        $variant->rating             = (string) (float) ($variant->rating ?? 0);
                        $variant->reviews            = (string) (int)   ($variant->reviews ?? 0);
                        $variant->discount_percentage = $variant->discount_percentage
                            ? (float) $variant->discount_percentage
                            : null;

                        $availableVariants[] = $variant;
                    }
                    $service->variants = $availableVariants;
                }
            }

            if ($service->has_variants == 0 || empty($service->variants)) {
                unset($service->variants);
                $service->has_variants = 0;
            }

            // Format image/media URLs
            $ext = strtolower(pathinfo($service->icon, PATHINFO_EXTENSION));
            $service->icon_type = in_array($ext, ['mp4', 'mov', 'avi', 'wmv']) ? 'video' : 'image';
            $service->icon = asset('uploads/service/' . $service->icon);
            
            if ($service->banner_media) {
                $service->banner_media = collect($service->banner_media)->map(function ($media) {
                    $media['url'] = asset('uploads/service-media/' . $media['url']);
                    if (!isset($media['type'])) {
                        $ext = strtolower(pathinfo($media['url'], PATHINFO_EXTENSION));
                        $media['type'] = in_array($ext, ['mp4', 'mov', 'avi', 'wmv']) ? 'video' : 'image';
                    }
                    $media['is_scroll_banner_image'] = isset($media['is_scroll_banner_image']) ? (int) $media['is_scroll_banner_image'] : 0;
                    return $media;
                });
            }

            if ($service->before_after) {
                $service->before_after = collect($service->before_after)->map(function ($image) {
                    $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
                    return [
                        'url' => asset('uploads/service-media/' . $image),
                        'type' => in_array($ext, ['mp4', 'mov', 'avi', 'wmv']) ? 'video' : 'image'
                    ];
                });
            }

            // content_json images
            if ($service->content_json) {
                $sections = $service->content_json;
                foreach ($sections as &$section) {
                    if (isset($section['steps'])) {
                        foreach ($section['steps'] as &$step) {
                            if (isset($step['image']) && !empty($step['image'])) {
                                $ext = strtolower(pathinfo($step['image'], PATHINFO_EXTENSION));
                                $step['image_type'] = in_array($ext, ['mp4', 'mov', 'avi', 'wmv']) ? 'video' : 'image';
                                $step['image'] = asset('uploads/service-content/' . $step['image']);
                            }
                        }
                    }
                    if (isset($section['image']) && !empty($section['image'])) {
                        $ext = strtolower(pathinfo($section['image'], PATHINFO_EXTENSION));
                        $section['image_type'] = in_array($ext, ['mp4', 'mov', 'avi', 'wmv']) ? 'video' : 'image';
                        $section['image'] = asset('uploads/service-content/' . $section['image']);
                    }
                    if (isset($section['items'])) {
                        foreach ($section['items'] as &$item) {
                            if (isset($item['image']) && !empty($item['image'])) {
                                $ext = strtolower(pathinfo($item['image'], PATHINFO_EXTENSION));
                                $item['image_type'] = in_array($ext, ['mp4', 'mov', 'avi', 'wmv']) ? 'video' : 'image';
                                $item['image'] = asset('uploads/service-content/' . $item['image']);
                            }
                        }
                    }

                    if (isset($section['type']) && $section['type'] === 'overview' && isset($section['essential_ids'])) {
                        $essentialIds = $section['essential_ids'];
                        $section['essentials'] = ServiceEssential::whereIn('id', (array)$essentialIds)
                            ->where('status', 1)
                            ->get()
                            ->map(function ($essential) {
                                return [
                                    'id' => $essential->id,
                                    'title' => $essential->title,
                                    'type' => $essential->type,
                                    'icon' => $essential->icon ? asset('uploads/essential/' . $essential->icon) : null,
                                ];
                            });
                    }
                }
                $service->content_json = $sections;
            }

            $service->is_popular = (int) $service->is_popular;
            $service->has_variants = (int) $service->has_variants;
            $service->status = (int) $service->status;

            // Get related popular services in the same category
            $relatedServices = DB::table('service_city_masters as scm')
                ->join('service_masters as sm', 'scm.service_master_id', '=', 'sm.id')
                ->select(
                    'sm.id',
                    'sm.name',
                    'scm.price',
                    'scm.discount_price',
                    'sm.duration',
                    'sm.rating',
                    'sm.reviews',
                    DB::raw('CONCAT("' . asset('uploads/service') . '/", sm.icon) AS icon'),
                    DB::raw('IF(sm.icon LIKE "%.mp4" OR sm.icon LIKE "%.mov" OR sm.icon LIKE "%.avi" OR sm.icon LIKE "%.wmv", "video", "image") AS icon_type'),
                    'sm.is_popular'
                )
                ->where('sm.category_id', $service->category_id)
                ->where('sm.id', '!=', $serviceId)
                ->where('sm.is_popular', 1)
                ->where('sm.status', 1)
                ->where('scm.city_id', $cityId)
                ->where('scm.status', 1)
                ->get()
                ->map(function ($item) {
                    $item->is_popular = (int) $item->is_popular;
                    return $item;
                });

            $service->related_services = $relatedServices;

            return $this->sendResponse(
                $service,
                'Service details retrieved successfully',
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

    /**
     * getServiceVariantDetails
     * "View Option" button click કર્યા પછી ખૂલે - Select Options screen
     * Service ની variant-wise full details return કરે (figma second screen)
     *
     * Required params: service_id, city_id
     */
    public function getServiceVariantDetails(Request $request): JsonResponse
    {
        $function_name = 'getServiceVariantDetails';

        try {
            $serviceId = $request->service_id;
            $cityId    = $request->city_id;

            if (!$serviceId || !$cityId) {
                return $this->sendError('Service ID and City ID are required.', $this->validation_error_status);
            }

            // ── Service master fetch ──────────────────────────────────────────
            $service = \App\Models\ServiceMaster::with(['category', 'subcategory', 'variants'])
                ->where('id', $serviceId)
                ->where('status', 1)
                ->first();

            if (!$service) {
                return $this->sendError('Service not found.', $this->backend_error_status);
            }

            if ($service->has_variants != 1) {
                return $this->sendError('This service does not have variants.', $this->validation_error_status);
            }

            // ── Category review stats ─────────────────────────────────────────
            $catStats = $this->getCategoryReviewStats($service->category_id);

            // ── Variant city prices ───────────────────────────────────────────
            $variantPrices = \App\Models\ServiceCityVariantPrice::where('service_master_id', $serviceId)
                ->where('city_id', $cityId)
                ->get()
                ->keyBy('variant_id');

            // ── Build variant list ────────────────────────────────────────────
            $variantsList = [];
            foreach ($service->variants as $variant) {
                $priceData = $variantPrices->get($variant->id);

                if ($priceData) {
                    if ($priceData->is_available == 0) {
                        continue; // Explicitly marked as unavailable in this city
                    }
                    $price           = (int) $priceData->price;
                    $discountPercent = (int) $priceData->discount_price;
                    $discountPrice   = (int) round($price + ($price * $discountPercent / 100));
                } else {
                    // Fallback to variant's default price if no city price record exists
                    $price           = (int) $variant->price;
                    $discountPercent = (int) $variant->discount_percentage;
                    $discountPrice   = (int) round($price + ($price * $discountPercent / 100));
                }

                // Thumbnail URL
                $thumbnailImage = $variant->thumbnail_image
                    ? asset('uploads/service-variant/' . $variant->thumbnail_image)
                    : null;

                $variantsList[] = [
                    'id'                  => $variant->id,
                    'service_master_id'   => $variant->service_master_id,
                    'name'                => $variant->name,
                    'duration'            => $variant->duration,
                    'description'         => $variant->description ?? null,
                    'thumbnail_image'     => $thumbnailImage,
                    'price'               => $price,
                    'discount_price'      => $discountPrice,
                    'discount_percentage' => $discountPercent,
                    'rating'              => (string) $catStats['rating'],
                    'reviews'             => (string) $catStats['reviews'],
                ];
            }

            if (empty($variantsList)) {
                return $this->sendError('No variants available for this service in the selected city.', $this->backend_error_status);
            }

            // ── Service icon ──────────────────────────────────────────────────
            $ext      = strtolower(pathinfo($service->icon, PATHINFO_EXTENSION));
            $iconType = in_array($ext, ['mp4', 'mov', 'avi', 'wmv']) ? 'video' : 'image';
            $iconUrl  = asset('uploads/service/' . $service->icon);

            // ── Banner media ──────────────────────────────────────────────────
            $bannerMedia = [];
            if ($service->banner_media) {
                $bannerMedia = collect($service->banner_media)->map(function ($media) {
                    $media['url'] = asset('uploads/service-media/' . $media['url']);
                    if (!isset($media['type'])) {
                        $ext          = strtolower(pathinfo($media['url'], PATHINFO_EXTENSION));
                        $media['type'] = in_array($ext, ['mp4', 'mov', 'avi', 'wmv']) ? 'video' : 'image';
                    }
                    $media['is_scroll_banner_image'] = isset($media['is_scroll_banner_image']) ? (int) $media['is_scroll_banner_image'] : 0;
                    return $media;
                })->values()->toArray();
            }

            // ── Page layout ───────────────────────────────────────────────────
            $pageLayout = [];

            // 1. Banner section first (as per Figma design)
            if (!empty($bannerMedia)) {
                $pageLayout[] = [
                    'type' => 'banner_section',
                    'data' => $bannerMedia,
                ];
            }

            // 2. Service header / info section
            $pageLayout[] = [
                'type' => 'service_info_section',
                'data' => [
                    'id'                => $service->id,
                    'name'              => $service->name,
                    'icon'              => $iconUrl,
                    'icon_type'         => $iconType,
                    'category_name'     => $service->category->name ?? '',
                    'sub_category_name' => $service->subcategory->name ?? '',
                    'rating'            => (string) $catStats['rating'],
                    'reviews'           => (string) $catStats['reviews'],
                    'has_variants'      => 1,
                    'starts_at'         => collect($variantsList)->min('price') ?? 0,
                    'total_option'      => count($variantsList),
                ],
            ];

            // 3. Variants list section
            $pageLayout[] = [
                'type' => 'variants_section',
                'data' => $variantsList,
            ];

            return $this->sendResponse(
                ['page_layout' => $pageLayout],
                'Service variant details retrieved successfully',
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
