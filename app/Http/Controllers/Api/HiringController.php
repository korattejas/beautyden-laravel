<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Exception;

class HiringController extends Controller
{
    protected mixed $success_status, $exception_status, $backend_error_status, $validation_error_status, $common_error_message;

    protected string $controller_name;

    public function __construct()
    {
        $this->controller_name = 'API/HiringController';
        $this->success_status = config('custom.status_code_for_success');
        $this->exception_status = config('custom.status_code_for_exception_error');
        $this->backend_error_status = config('custom.status_code_for_backend_error');
        $this->validation_error_status = config('custom.status_code_for_validation_error');
        $this->common_error_message = config('custom.common_error_message');
    }

    public function getHiring(): JsonResponse
    {
        $function_name = 'getHiring';

        try {
            $hirings = DB::table('hirings as h')
                ->select(
                    'h.id',
                    'h.title',
                    'h.description',
                    'h.city',
                    'h.min_experience',
                    'h.max_experience',
                    'h.salary_range',
                    'h.experience_level',
                    'h.hiring_type',
                    'h.gender_preference',
                    DB::raw('JSON_EXTRACT(h.required_skills, "$") as required_skills'),
                    'h.is_popular',
                )
                ->where('h.status', 1)
                ->orderByDesc('h.is_popular')
                ->get()
                ->map(function ($hiring) {
                    $hiring->required_skills = $hiring->required_skills
                        ? json_decode($hiring->required_skills, true)
                        : [];

                    $hiring->experience_level_text = match ($hiring->experience_level) {
                        1 => 'Fresher',
                        2 => 'Experienced',
                        3 => 'Expert',
                        default => 'N/A'
                    };

                    $hiring->hiring_type_text = match ($hiring->hiring_type) {
                        1 => 'Full-time',
                        2 => 'Part-time',
                        3 => 'Internship',
                        4 => 'Work from home',
                        default => 'N/A'
                    };

                    $hiring->gender_preference_text = match ($hiring->gender_preference) {
                        1 => 'Female',
                        2 => 'Male',
                        3 => 'Any',
                        default => 'N/A'
                    };

                    return $hiring;
                });

            if ($hirings->isEmpty()) {
                return $this->sendError('No hiring found.', $this->backend_error_status);
            }

            return $this->sendResponse(
                $hirings,
                'Hiring jobs retrieved successfully',
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
