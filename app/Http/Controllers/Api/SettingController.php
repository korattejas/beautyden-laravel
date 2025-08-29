<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Exception;

class SettingController extends Controller
{
    protected mixed $success_status, $exception_status, $backend_error_status, $validation_error_status, $common_error_message;

    protected string $controller_name;

    public function __construct()
    {
        $this->controller_name = 'API/SettingController';
        $this->success_status = config('custom.status_code_for_success');
        $this->exception_status = config('custom.status_code_for_exception_error');
        $this->backend_error_status = config('custom.status_code_for_backend_error');
        $this->validation_error_status = config('custom.status_code_for_validation_error');
        $this->common_error_message = config('custom.common_error_message');
    }

    public function getsettings(): JsonResponse
    {
        $function_name = 'getsettings';

        try {
            $faqs = DB::table('settings as s')
                ->select(
                    's.id',
                    's.screen_name',
                    's.key',
                    's.value',
                )
                ->where('s.status', 1)
                ->orderBy('s.id', 'ASC')
                ->get();

            if ($faqs->isEmpty()) {
                return $this->sendError('No Setting found.', $this->backend_error_status);
            }

            return $this->sendResponse(
                $faqs,
                'Settings retrieved successfully',
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
