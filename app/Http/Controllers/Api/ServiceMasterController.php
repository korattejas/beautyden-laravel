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
                    'scm.price',
                    'scm.discount_price',
                    'sm.duration',
                    'sm.rating',
                    'sm.reviews',
                    'sm.description',
                    DB::raw('CONCAT("' . asset('uploads/service') . '/", sm.icon) AS icon'),
                    DB::raw('IF(sm.icon LIKE "%.mp4" OR sm.icon LIKE "%.mov" OR sm.icon LIKE "%.avi" OR sm.icon LIKE "%.wmv", "video", "image") AS icon_type'),
                    'sm.is_popular',
                    'sm.has_variants'
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
                ->paginate($perPage, ['*'], 'page', $page)
                ->through(function ($service) {
                    $service->is_popular = (int) $service->is_popular;
                    $service->has_variants = (int) $service->has_variants;
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
                    $service->price = $cityService->price;
                    $service->discount_price = $cityService->discount_price;
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

            $pageLayout = [];

            // 1. Service Basic Details (Hero Section)
            $pageLayout[] = [
                'type' => 'service_info_section',
                'data' => [
                    'id' => $service->id,
                    'name' => $service->name,
                    'price' => $service->price ?? 0,
                    'discount_price' => $service->discount_price ?? 0,
                    'duration' => $service->duration,
                    'rating' => $service->rating,
                    'reviews' => $service->reviews,
                    'icon' => $service->icon,
                    'icon_type' => $service->icon_type,
                    'category_name' => $service->category_name,
                    'sub_category_name' => $service->sub_category_name,
                    'is_popular' => $service->is_popular,
                    'status' => $service->status,
                    'has_variants' => $service->has_variants,
                    'variants' => $service->variants ?? []
                ]
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

                $subCategories = \App\Models\ServiceSubcategory::where('category_id', $service->category_id)
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
                    $service->price = $cityService->price;
                    $service->discount_price = $cityService->discount_price;
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
}
