<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Exception;

class ServiceController extends Controller
{
    protected mixed $success_status, $exception_status, $backend_error_status, $validation_error_status, $common_error_message;

    protected string $controller_name;

    public function __construct()
    {
        $this->controller_name = 'API/ServiceController';
        $this->success_status = config('custom.status_code_for_success');
        $this->exception_status = config('custom.status_code_for_exception_error');
        $this->backend_error_status = config('custom.status_code_for_backend_error');
        $this->validation_error_status = config('custom.status_code_for_validation_error');
        $this->common_error_message = config('custom.common_error_message');
    }

    // public function getServiceCategory(): JsonResponse
    // {
    //     $function_name = 'getServiceCategory';
    //     try {
    //         $categories = DB::table('service_categories as c')
    //             ->select(
    //                 'c.id',
    //                 'c.name',
    //                 DB::raw('CONCAT("' . asset('uploads/service-category') . '/", c.icon) AS icon'),
    //                 'c.description',
    //                 'c.is_popular'
    //             )
    //             ->where('c.status', 1)
    //             ->orderBy('c.is_popular', 'desc')
    //             ->get();

    //         if ($categories->isEmpty()) {
    //             return $this->sendError('No category found.', $this->backend_error_status);
    //         }

    //         $subCategories = DB::table('service_subcategories as sc')
    //             ->select(
    //                 'sc.id',
    //                 'sc.service_category_id',
    //                 'sc.name',
    //                 DB::raw('CONCAT("' . asset('uploads/service-subcategory') . '/", sc.icon) AS icon'),
    //                 'sc.description',
    //                 'sc.is_popular'
    //             )
    //             ->where('sc.status', 1)
    //             ->get();

    //         $categories->transform(function ($category) use ($subCategories) {
    //             $category->subcategories = $subCategories->where('service_category_id', $category->id)->values();
    //             return $category;
    //         });

    //         return $this->sendResponse($categories, 'Categories with subcategories retrieved successfully', $this->success_status);
    //     } catch (Exception $e) {
    //         logCatchException($e, $this->controller_name, $function_name);
    //         return $this->sendError($this->common_error_message, $this->exception_status);
    //     }
    // }

    public function getServiceCategory(): JsonResponse
    {
        $function_name = 'getServiceCategory';
        try {
            $categories = DB::table('service_categories')->select(
                'id',
                'name',
                DB::raw('CONCAT("' . asset('uploads/service-category') . '/", icon) AS icon'),
                'description',
                'is_popular'
            )
                ->where('status', 1)
                ->orderBy('is_popular', 'desc')
                ->get();

            if ($categories->isEmpty()) {
                return $this->sendError('No category found.', $this->backend_error_status);
            }

            return $this->sendResponse($categories, 'Categories retrieved successfully', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }


    public function getServices(Request $request): JsonResponse
    {
        $function_name = 'getServices';

        try {
            $cityId = $request->city_id ?? null;

            if ($cityId) {
                $query = DB::table('service_city_prices as scp')
                    ->join('services as s', 'scp.service_id', '=', 's.id')
                    ->join('service_categories as c', 'scp.category_id', '=', 'c.id')
                    ->leftJoin('service_subcategories as csc', 'scp.sub_category_id', '=', 'csc.id')
                    ->where('scp.city_id', $cityId)
                    ->select(
                        's.id',
                        'scp.category_id',
                        's.sub_category_id',
                        'c.name as category_name',
                        'csc.name as sub_category_name',
                        's.name',
                        'scp.price',
                        'scp.discount_price',
                        's.duration',
                        's.rating',
                        's.reviews',
                        's.description',
                        's.includes',
                        DB::raw('CONCAT("' . asset('uploads/service') . '/", s.icon) AS icon'),
                        's.is_popular'
                    )
                    ->where('scp.status', 1);
            } else {
                $query = DB::table('services as s')
                    ->join('service_categories as c', 's.category_id', '=', 'c.id')
                    ->leftJoin('service_subcategories as csc', 's.sub_category_id', '=', 'csc.id')
                    ->select(
                        's.id',
                        's.category_id',
                        's.sub_category_id',
                        'c.name as category_name',
                        'csc.name as sub_category_name',
                        's.name',
                        's.price',
                        's.discount_price',
                        's.duration',
                        's.rating',
                        's.reviews',
                        's.description',
                        's.includes',
                        DB::raw('CONCAT("' . asset('uploads/service') . '/", s.icon) AS icon'),
                        's.is_popular'
                    )
                    ->where('s.status', 1);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('s.name', 'like', "%$search%")
                        ->orWhere('s.description', 'like', "%$search%")
                        ->orWhere('c.name', 'like', "%$search%")
                        ->orWhere('s.duration', 'like', "%$search%")
                        ->orWhere('s.discount_price', 'like', "%$search%")
                        ->orWhere('s.price', 'like', "%$search%")
                        ->orWhere('s.reviews', 'like', "%$search%")
                        ->orWhere('s.includes', 'like', "%$search%")
                        ->orWhere('s.rating', 'like', "%$search%");
                });
            }

            if ($request->filled('category_id')) {
                $query->where('s.category_id', $request->category_id);
            }

            if ($request->filled('sub_category_id')) {
                $query->where('s.sub_category_id', $request->sub_category_id);
            }

            $perPage = $request->per_page ?? 8;
            $page = $request->page ?? 1;

            $services = $query->orderByDesc('s.is_popular')
                ->paginate($perPage, ['*'], 'page', $page)
                ->through(function ($service) {
                    $service->includes = $service->includes ? json_decode($service->includes, true) : [];
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
}
