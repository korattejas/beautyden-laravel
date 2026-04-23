<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Offer;
use Illuminate\Http\JsonResponse;
use Exception;

class OfferController extends Controller
{
    protected mixed $success_status, $exception_status, $backend_error_status, $validation_error_status, $common_error_message;
    protected string $controller_name;

    public function __construct()
    {
        $this->controller_name = 'API/OfferController';
        $this->success_status = config('custom.status_code_for_success');
        $this->exception_status = config('custom.status_code_for_exception_error');
        $this->backend_error_status = config('custom.status_code_for_backend_error');
        $this->validation_error_status = config('custom.status_code_for_validation_error');
        $this->common_error_message = config('custom.common_error_message');
    }

    public function getOffers(Request $request): JsonResponse
    {
        $function_name = 'getOffers';

        try {
            $position = $request->input('position');
            
            $query = Offer::where('status', 1);
            
            if ($position) {
                $query->where('position', $position);
            }
            
            $offers = $query->orderBy('priority', 'asc')->get();
            
            if ($offers->isEmpty()) {
                return $this->sendError('No offers found.', $this->backend_error_status);
            }

            $offers->transform(function ($offer) {
                $media = [];
                if (!empty($offer->media)) {
                    $dir = $offer->media_type == 'image' ? 'uploads/offers/images/' : 'uploads/offers/videos/';
                    foreach ($offer->media as $file) {
                        $media[] = asset($dir . $file);
                    }
                }
                $offer->media_urls = $media;
                return $offer;
            });

            return $this->sendResponse(
                $offers,
                'Offers retrieved successfully',
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
