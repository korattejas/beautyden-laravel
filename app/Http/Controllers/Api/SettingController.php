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
            $settings = DB::table('settings as s')
                ->select('s.id', 's.screen_name', 's.key', 's.value')
                ->where('s.status', 1)
                ->orderBy('s.id', 'ASC')
                ->get();

            if ($settings->isEmpty()) {
                return $this->sendError('No Setting found.', $this->backend_error_status);
            }

            $heroSlides = [
                [
                    'image' => asset('uploads/home-page-slider/slider1.webp')
                ],
                [
                    'image' => asset('uploads/home-page-slider/slider2.webp')
                ],
                [
                    'image' => asset('uploads/home-page-slider/slider2.webp')
                ],
            ];

            $heroContent = [
                'title' => "Trusted Beauty Service at Your Doorstep",
                'description' => "Experience premium beauty treatments from head to toe — hair, skin, nails, waxing, facials, spa therapies, and more, all delivered by expert professionals at your doorstep.",
                'badges' => [
                    "Home",
                    "Events",
                    "Functions",
                    "Marriages",
                    "Special Occasions",
                ],
            ];

            return $this->sendResponse(
                [
                    'settings' => $settings,
                    'homePageSlides' => $heroSlides,
                    'homePageSlidesContent' => $heroContent
                ],
                'Settings and Hero content retrieved successfully',
                $this->success_status
            );
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);

            return $this->sendError(
                $this->common_error_message,
                $this->exception_status
            );
        }
    }
}
