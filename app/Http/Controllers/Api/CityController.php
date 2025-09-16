<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Exception;

class CityController extends Controller
{
    protected mixed $success_status, $exception_status, $backend_error_status, $validation_error_status, $common_error_message;

    protected string $controller_name;

    public function __construct()
    {
        $this->controller_name = 'API/CityController';
        $this->success_status = config('custom.status_code_for_success');
        $this->exception_status = config('custom.status_code_for_exception_error');
        $this->backend_error_status = config('custom.status_code_for_backend_error');
        $this->validation_error_status = config('custom.status_code_for_validation_error');
        $this->common_error_message = config('custom.common_error_message');
    }

    public function getCities(): JsonResponse
    {
        $function_name = 'getCities';

        try {
            $cities = DB::table('cities as c')
                ->select(
                    'c.id',
                    'c.name',
                    'c.state',
                    'c.area',
                    'c.slug',
                    DB::raw('CONCAT("' . asset('uploads/city') . '/", c.icon) AS icon'),
                    'c.launch_quarter',
                    'c.status',
                    'c.is_popular',
                )
                // ->where('c.status', 1)
                ->orderByDesc('c.is_popular')
                ->orderBy('c.name', 'ASC')
                ->get();

            if ($cities->isEmpty()) {
                return $this->sendError('No cities found.', $this->backend_error_status);
            }

            return $this->sendResponse(
                $cities,
                'Cities retrieved successfully',
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
