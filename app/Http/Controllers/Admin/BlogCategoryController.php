<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Helpers\ImageUploadHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;


class BlogCategoryController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/BlogCategoryController";
    }
    public function index()
    {
        $function_name = 'index';
        try {
            return view('admin.blog-category.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        $function_name = 'create';
        try {
            return view('admin.blog-category.create');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        $function_name = 'edit';
        try {
            $category = BlogCategory::where('id', decryptId($id))->first();
            if ($category) {
                return view('admin.blog-category.edit', [
                    'category' => $category
                ]);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataBlogCategory(Request $request)
    {
        $function_name = 'getDataBlogCategory';
        try {
            if ($request->ajax()) {
                $categories = DB::table('blog_categories')->select('blog_categories.*');
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
                            'edit_route' => route('admin.blog-category.edit', encryptId($categories->id)),
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
                       if ($categories->icon && file_exists(public_path('uploads/blog-category/' . $categories->icon))) {
                            $imageUrl = asset('uploads/blog-category/' . $categories->icon);
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
                    $id == 0 ? 'unique:blog_categories,name' : 'unique:blog_categories,name,' . $id . ',id',
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
                    $icon = ImageUploadHelper::blogCategoryimageUpload($request->icon);
                }

                BlogCategory::create([
                    'name' => $request->name,
                    'description' => $request->description,
                    'icon' => $icon ?? null,
                    'is_popular' => (int) $request->is_popular,
                    'status' => (int) $request->status,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "Blog category added successfully"
                ]);
            } else {
                $category = BlogCategory::where('id', $id)->first();

                if ($request->hasFile('icon')) {
                    $icon = ImageUploadHelper::blogCategoryimageUpload($request->icon);
                } else {
                    $icon = $category->icon;
                }

                BlogCategory::where('id', $id)->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'icon' => $icon,
                    'is_popular' => (int) $request->is_main,
                    'status' => (int) $request->status,
                ]);
                return response()->json([
                    'success' => true,
                    'message' => "Blog category edited successfully"
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
            BlogCategory::where('id', $id)->update(['status' => $status]);
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
            BlogCategory::where('id', $id)->update(['is_popular' => $status]);
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
            $category = BlogCategory::where('id', $id)->first();
            if ($category) {
                $filePath = public_path('uploads/blog-category/' . $category->icon);

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
