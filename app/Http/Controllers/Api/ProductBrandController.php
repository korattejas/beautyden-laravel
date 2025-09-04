<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Exception;

class ProductBrandController extends Controller
{
    protected mixed $success_status, $exception_status, $backend_error_status, $validation_error_status, $common_error_message;

    protected string $controller_name;

    public function __construct()
    {
        $this->controller_name = 'API/ProductBrandController';
        $this->success_status = config('custom.status_code_for_success');
        $this->exception_status = config('custom.status_code_for_exception_error');
        $this->backend_error_status = config('custom.status_code_for_backend_error');
        $this->validation_error_status = config('custom.status_code_for_validation_error');
        $this->common_error_message = config('custom.common_error_message');
    }

    public function getProductBrand(): JsonResponse
    {
        $function_name = 'getProductBrand';

        try {
            $cities = DB::table('product_brands as p')
                ->select(
                    'p.id',
                    'p.name',
                    DB::raw('CONCAT("' . asset('uploads/product-brand') . '/", p.icon) AS icon'),
                )
                ->where('p.status', 1)
                ->orderBy('p.name', 'ASC')
                ->get();

            if ($cities->isEmpty()) {
                return $this->sendError('No product brand found.', $this->backend_error_status);
            }

            return $this->sendResponse(
                $cities,
                'Product Brand retrieved successfully',
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
