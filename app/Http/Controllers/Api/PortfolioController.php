<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Exception;

class PortfolioController extends Controller
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

    public function getPortfolio(): JsonResponse
    {
        $function_name = 'getPortfolio';

        try {
            $portfolios = DB::table('portfolios')
                ->select('id', 'name', 'photos')
                ->where('status', 1)
                ->orderBy('name', 'ASC')
                ->get();

            if ($portfolios->isEmpty()) {
                return $this->sendError(
                    'No portfolio found.',
                    $this->backend_error_status
                );
            }

            $data = $portfolios->map(function ($portfolio) {

                $photos = [];

                if (!empty($portfolio->photos)) {
                    $decoded = json_decode($portfolio->photos, true);

                    if (is_array($decoded)) {
                        foreach ($decoded as $img) {
                            $photos[] = asset('uploads/portfolio/' . $img);
                        }
                    }
                }

                return [
                    'id'     => $portfolio->id,
                    'name'   => $portfolio->name,
                    'photos' => $photos,
                ];
            });

            return $this->sendResponse(
                $data,
                'Portfolio retrieved successfully',
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
