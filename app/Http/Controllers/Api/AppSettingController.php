<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class AppSettingController extends Controller
{
    protected mixed $success_status, $exception_status, $backend_error_status, $validation_error_status, $common_error_message;
    protected string $controller_name;

    public function __construct()
    {
        $this->controller_name = 'API/AppSettingController';
        $this->success_status = config('custom.status_code_for_success');
        $this->exception_status = config('custom.status_code_for_exception_error');
        $this->backend_error_status = config('custom.status_code_for_backend_error');
        $this->validation_error_status = config('custom.status_code_for_validation_error');
        $this->common_error_message = config('custom.common_error_message');
    }

    /**
     * Get all active app settings
     */
    public function getAppSettings(): JsonResponse
    {
        $function_name = 'getAppSettings';

        try {
            $settings = AppSetting::where('status', 1)
                ->orderBy('id', 'ASC')
                ->get();

            $settings->transform(function ($setting) {
                $setting->image = !empty($setting->image) ? asset('uploads/app-settings/' . $setting->image) : '';
                return $setting;
            });

            if ($settings->isEmpty()) {
                return $this->sendError('No App Settings found.', $this->backend_error_status);
            }

            return $this->sendResponse(
                $settings,
                'App settings retrieved successfully',
                $this->success_status
            );

        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }
}
