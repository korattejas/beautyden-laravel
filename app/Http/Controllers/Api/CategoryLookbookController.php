<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Exception;

class CategoryLookbookController extends Controller
{
    protected mixed $success_status, $exception_status, $backend_error_status, $validation_error_status, $common_error_message;

    protected string $controller_name;

    public function __construct()
    {
        $this->controller_name = 'API/CategoryLookbookController';
        $this->success_status = config('custom.status_code_for_success');
        $this->exception_status = config('custom.status_code_for_exception_error');
        $this->backend_error_status = config('custom.status_code_for_backend_error');
        $this->validation_error_status = config('custom.status_code_for_validation_error');
        $this->common_error_message = config('custom.common_error_message');
    }

    public function getCategoryLookbooks(Request $request): JsonResponse
    {
        $function_name = 'getCategoryLookbooks';

        try {
            $query = DB::table('category_lookbooks')
                ->join('service_categories', 'category_lookbooks.category_id', '=', 'service_categories.id')
                ->leftJoin('service_subcategories', 'category_lookbooks.sub_category_id', '=', 'service_subcategories.id')
                ->select(
                    'category_lookbooks.id', 
                    'category_lookbooks.category_id', 
                    'service_categories.name as category_name', 
                    'category_lookbooks.sub_category_id',
                    'service_subcategories.name as sub_category_name',
                    'category_lookbooks.photos'
                )
                ->where('category_lookbooks.status', 1)
                ->where('service_categories.status', 1);
                
            if ($request->has('category_id') && $request->has('sub_category_id')) {
                $query->where('category_lookbooks.category_id', $request->category_id)
                      ->where(function($q) use ($request) {
                          $q->where('category_lookbooks.sub_category_id', $request->sub_category_id)
                            ->orWhereNull('category_lookbooks.sub_category_id');
                      });
            } elseif ($request->has('category_id')) {
                $query->where('category_lookbooks.category_id', $request->category_id);
            } elseif ($request->has('sub_category_id')) {
                $query->where('category_lookbooks.sub_category_id', $request->sub_category_id);
            }

            $lookbooks = $query->orderBy('service_categories.name', 'ASC')->get();

            if ($lookbooks->isEmpty()) {
                return $this->sendError(
                    'No category lookbook found.',
                    $this->backend_error_status
                );
            }

            $data = $lookbooks->map(function ($lookbook) {
                $photos = [];

                if (!empty($lookbook->photos)) {
                    $decoded = json_decode($lookbook->photos, true);

                    if (is_array($decoded)) {
                        foreach ($decoded as $img) {
                            $photos[] = asset('uploads/portfolio/' . $img);
                        }
                    }
                }

                return [
                    'id'                => $lookbook->id,
                    'category_id'       => $lookbook->category_id,
                    'category_name'     => $lookbook->category_name,
                    'sub_category_id'   => $lookbook->sub_category_id,
                    'sub_category_name' => $lookbook->sub_category_name,
                    'photos'            => $photos,
                ];
            });

            return $this->sendResponse(
                $data,
                'Category lookbooks retrieved successfully',
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
