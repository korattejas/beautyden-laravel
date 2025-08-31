<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Helpers\ImageUploadHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;


class ServiceCategoryController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/ServiceCategoryController";
    }
    public function index()
    {
        $function_name = 'index';
        try {
            return view('admin.service-category.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        $function_name = 'create';
        try {
            return view('admin.service-category.create');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        $function_name = 'edit';
        try {
            $category = ServiceCategory::where('id', decryptId($id))->first();
            if ($category) {
                return view('admin.service-category.edit', [
                    'category' => $category
                ]);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataServiceCategory(Request $request)
    {
        $function_name = 'getDataServiceCategory';
        try {
            if ($request->ajax()) {
                $categories = DB::table('service_categories')->select('service_categories.*');
                return DataTables::of($categories)
                    ->addColumn('status', function ($categories) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => $categories->status
                        ];
                        return view('admin.render-view.datable-label', [
                            'status_array' => $status_array
                        ])->render();
                    })
                    ->addColumn('is_popular', function ($categories) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => 3,
                            'current_is_popular_priority_status' => $categories->is_popular
                        ];
                        return view('admin.render-view.datable-label', [
                            'status_array' => $status_array
                        ])->render();
                    })
                    ->addColumn('action', function ($categories) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'edit_route' => route('admin.service-category.edit', encryptId($categories->id)),
                            'delete_id' => $categories->id,
                            'current_status' => $categories->status,
                            'current_is_popular_priority_status' => $categories->is_popular,
                            'hidden_id' => $categories->id,
                        ];
                        return view('admin.render-view.datable-action', [
                            'action_array' => $action_array
                        ])->render();
                    })
                   ->addColumn('icon', function ($categories) {
                        if ($categories->icon && file_exists(public_path('uploads/service-category/' . $categories->icon))) {
                            $imageUrl = asset('uploads/service-category/' . $categories->icon);
                            return '<img src="' . $imageUrl . '" style="max-width:100px;" alt="Category Icon" />';
                        }
                        return ''; 
                    })

                    ->rawColumns(['action', 'icon', 'status', 'is_popular'])
                    ->make(true);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }


    public function store(Request $request)
    {
        $function_name = 'store';
        $request_all = request()->all();
        try {
            $id = $request->input('edit_value');
            $validateArray = [
                'name' => [
                    'required',
                    $id == 0 ? 'unique:service_categories,name' : 'unique:service_categories,name,' . $id . ',id',
                ],
                'icon' => $id == 0 ? 'image|mimes:jpeg,png,jpg,gif,svg' : 'image|mimes:jpeg,png,jpg,gif,svg',
            ];

            $validateMessage = [
                'name.required' => 'The category name is required.',
                'name.unique' => 'The category name has already been taken.',
                'icon.image' => 'The file must be an image.',
                'icon.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, svg.',
            ];


            $validator = Validator::make($request_all, $validateArray, $validateMessage);
            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
            }

            if ($id == 0) {
                if ($request->hasFile('icon')) {
                    $icon = ImageUploadHelper::serviceCategoryimageUpload($request->icon);
                }

                ServiceCategory::create([
                    'name' => $request->name,
                    'description' => $request->description,
                    'icon' => $icon ?? null,
                    'is_popular' => (int) $request->is_popular,
                    'status' => (int) $request->status,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "Service category added successfully"
                ]);
            } else {
                $category = ServiceCategory::where('id', $id)->first();

                if ($request->hasFile('icon')) {
                    $filePath = public_path('uploads/service-category/' . $category->icon);

                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                    $icon = ImageUploadHelper::serviceCategoryimageUpload($request->icon);
                } else {
                    $icon = $category->icon;
                }

                ServiceCategory::where('id', $id)->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'icon' => $icon,
                    'is_popular' => (int) $request->is_main,
                    'status' => (int) $request->status,
                ]);
                return response()->json([
                    'success' => true,
                    'message' => "Service category edited successfully"
                ]);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function changeStatus($id, $status)
    {
        $function_name = 'changeStatus';
        try {
            ServiceCategory::where('id', $id)->update(['status' => $status]);
            return response()->json(['message' => trans('admin_string.msg_status_change')]);
        } catch (\Exception $e) {
            logger()->error("$function_name: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function changePriorityStatus($id, $status)
    {
        $function_name = 'changePriorityStatus';
        try {
            ServiceCategory::where('id', $id)->update(['is_popular' => $status]);
            return response()->json([
                'message' => trans('admin_string.msg_priority_status_change')
            ]);
        } catch (\Exception $e) {
            logger()->error("$function_name: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function destroy(int $id)
    {
        $function_name = 'destroy';
        try {
            $category = ServiceCategory::where('id', $id)->first();
            if ($category) {
                $filePath = public_path('uploads/service-category/' . $category->icon);

                if (File::exists($filePath)) {
                    File::delete($filePath);
                }

                $category->delete();

                return response()->json([
                    'message' => trans('admin_string.category_deleted_successfully')
                ]);
            } else {
                logger()->error("$function_name: Failed to delete the image from S3 or no category found.");
                return response()->json(['error' => 'Failed to delete the image from S3 or no category found.'], 500);
            }
        } catch (\Exception $e) {
            logger()->error("$function_name: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }
}
