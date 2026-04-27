<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceMaster;
use App\Models\ServiceCityMaster;
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
            $cityId = $request->city_id ?? null;

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
                    'sm.is_popular'
                )
                ->where('scm.status', 1)
                ->where('sm.status', 1);

            if ($cityId) {
                $query->where('scm.city_id', $cityId);
            }

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

            if (!$serviceId) {
                return $this->sendError('Service ID is required.', $this->validation_error_status);
            }

            $service = ServiceMaster::with(['category', 'subcategory'])
                ->where('id', $serviceId)
                ->where('status', 1)
                ->first();

            if (!$service) {
                return $this->sendError('Service not found.', $this->backend_error_status);
            }

            $service->category_name = $service->category->name ?? '';
            $service->sub_category_name = $service->subcategory->name ?? '';

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
            }

            // Format image/media URLs
            $service->icon = asset('uploads/service/' . $service->icon);
            
            if ($service->banner_media) {
                $service->banner_media = collect($service->banner_media)->map(function ($media) {
                    $media['url'] = asset('uploads/service/' . $media['url']);
                    return $media;
                });
            }

            if ($service->before_after) {
                $service->before_after = collect($service->before_after)->map(function ($image) {
                    return asset('uploads/service/' . $image);
                });
            }

            // content_json images
            if ($service->content_json) {
                $sections = $service->content_json;
                foreach ($sections as &$section) {
                    if (isset($section['steps'])) {
                        foreach ($section['steps'] as &$step) {
                            if (isset($step['image'])) {
                                $step['image'] = asset('uploads/service/' . $step['image']);
                            }
                        }
                    }
                    if (isset($section['image'])) {
                        $section['image'] = asset('uploads/service/' . $section['image']);
                    }
                    if (isset($section['items'])) {
                        foreach ($section['items'] as &$item) {
                            if (isset($item['image'])) {
                                $item['image'] = asset('uploads/service/' . $item['image']);
                            }
                        }
                    }
                }
                $service->content_json = $sections;
            }

            $service->is_popular = (int) $service->is_popular;
            $service->status = (int) $service->status;

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
