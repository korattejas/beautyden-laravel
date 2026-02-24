<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Portfolio;
use App\Helpers\ImageUploadHelper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class PortfolioController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/PortfolioController";
    }
    public function index()
    {
        $function_name = 'index';
        try {
            return view('admin.portfolio.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        $function_name = 'create';
        try {
            return view('admin.portfolio.create');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        $function_name = 'edit';
        try {
            $portfolio = Portfolio::where('id', decryptId($id))->first();
            if ($portfolio) {
                return view('admin.portfolio.edit', [
                    'portfolio' => $portfolio
                ]);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function store(Request $request)
    {
        $function_name = 'store';
        $request_all = $request->all();

        try {
            $id = (int) $request->input('edit_value', 0);

            $validateArray = [
                'name' => [
                    'required',
                    $id == 0
                        ? 'unique:portfolios,name'
                        : 'unique:portfolios,name,' . $id . ',id',
                ],
                'photos'   => 'nullable|array',
                'photos.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp',
            ];

            $validateMessage = [
                'name.required'   => 'The portfolio name is required.',
                'name.unique'     => 'The portfolio name has already been taken.',
                'photos.*.image'  => 'Each file must be an image.',
                'photos.*.mimes'  => 'Images must be jpeg, png, jpg, gif, svg, webp.',
            ];

            $validator = Validator::make($request_all, $validateArray, $validateMessage);
            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], $this->validator_error_code);
            }

            $storedPhotos = [];
            $portfolio = null;

            if ($id !== 0) {
                $portfolio = Portfolio::find($id);
                if ($portfolio && is_array($portfolio->photos)) {
                    $storedPhotos = $portfolio->photos;
                }
            }

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $filename = ImageUploadHelper::PortfolioImageUpload($photo);
                    $storedPhotos[] = $filename;
                }
            }

            $data = [
                'name'   => $request->name,
                'photos' => !empty($storedPhotos) ? $storedPhotos : null,
                'status' => (int) $request->input('status', 1),
            ];

            if ($id === 0) {
                Portfolio::create($data);
                $msg = 'Portfolio added successfully';
            } else {
                $portfolio->update($data);
                $msg = 'Portfolio updated successfully';
            }

            return response()->json([
                'success' => true,
                'message' => $msg
            ]);
        } catch (\Exception $e) {
            logger()->error("Portfolio store error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $this->error_message
            ], $this->exception_error_code);
        }
    }



    public function getDataPortfolio(Request $request)
    {
        $function_name = 'getDataPortfolio';

        try {
            if ($request->ajax()) {

                $portfolios = Portfolio::query();

                return DataTables::of($portfolios)

                    ->addColumn('status', function ($portfolio) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status'   => $portfolio->status
                        ];

                        return view('admin.render-view.datable-label', [
                            'status_array' => $status_array
                        ])->render();
                    })

                    ->addColumn('action', function ($portfolio) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'edit_route'      => route('admin.portfolio.edit', encryptId($portfolio->id)),
                            'delete_id'       => $portfolio->id,
                            'current_status'  => $portfolio->status,
                            'hidden_id'       => $portfolio->id,
                        ];

                        return view('admin.render-view.datable-action', [
                            'action_array' => $action_array
                        ])->render();
                    })

                    ->addColumn('photos', function ($portfolio) {

                        $html = '<div style="display:flex;gap:6px;flex-wrap:wrap;">';

                        foreach ($portfolio->photos as $img) {
                            $path = public_path('uploads/portfolio/' . $img);

                            if (file_exists($path)) {
                                $url = asset('uploads/portfolio/' . $img);
                                $html .= '<img src="' . $url . '"
                                style="width:60px;height:60px;object-fit:cover;border-radius:4px;border:1px solid #ddd;" />';
                            }
                        }

                        $html .= '</div>';

                        return $html;
                    })

                    ->rawColumns(['status', 'action', 'photos'])
                    ->make(true);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);

            return response()->json([
                'error' => $this->error_message
            ], $this->exception_error_code);
        }
    }




    public function changeStatus($id, $status)
    {
        $function_name = 'changeStatus';
        try {
            Portfolio::where('id', $id)->update(['status' => $status]);
            return response()->json(['message' => trans('admin_string.msg_status_change')]);
        } catch (\Exception $e) {
            logger()->error("$function_name: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function destroy(int $id)
    {
        $function_name = 'destroy';

        try {
            $portfolio = Portfolio::find($id);

            if (!$portfolio) {
                return response()->json([
                    'error' => 'Portfolio not found'
                ], 404);
            }

            if (!empty($portfolio->photos) && is_array($portfolio->photos)) {
                foreach ($portfolio->photos as $photo) {
                    $filePath = public_path('uploads/portfolio/' . $photo);

                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                }
            }

            $portfolio->delete();

            return response()->json([
                'success' => true,
                'message' => 'Portfolio deleted successfully'
            ]);
        } catch (\Exception $e) {
            logger()->error("$function_name: " . $e->getMessage());

            return response()->json([
                'error' => $this->error_message
            ], $this->exception_error_code);
        }
    }
}
