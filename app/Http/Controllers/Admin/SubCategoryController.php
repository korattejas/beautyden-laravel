<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use App\Helpers\ImageUploadHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;



class SubCategoryController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/SubCategoryController";
    }
    public function index()
    {
        $function_name = 'index';
        try {
            return view('admin.subCategory.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        $function_name = 'create';
        try {
            $categories = Category::where('status', 1)->select('id', 'name')->get();
            return view('admin.subCategory.create', ['categories' => $categories]);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        $function_name = 'edit';
        try {
            $categories = Category::where('status', 1)->select('id', 'name')->get();
            $subCategory = SubCategory::where('id', decryptId($id))->first();
            if ($subCategory) {
                return view('admin.subCategory.edit', [
                    'categories' => $categories,
                    'subCategory' => $subCategory
                ]);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataSubCategory(Request $request)
    {
        $function_name = 'getDataSubCategory';
        try {
            if ($request->ajax()) {
                $subCategories = DB::table('sub_categories')->leftJoin('categories', 'categories.id', 'sub_categories.category_id')
                    ->select('sub_categories.*', 'categories.name as category_name');
                return DataTables::of($subCategories)
                    ->addColumn('status', function ($subCategories) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => $subCategories->status
                        ];
                        return view('admin.render-view.datable-label', [
                            'status_array' => $status_array
                        ])->render();
                    })
                    ->addColumn('is_main', function ($subCategories) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => 3,
                            'current_is_main_priority_status' => $subCategories->is_main
                        ];
                        return view('admin.render-view.datable-label', [
                            'status_array' => $status_array
                        ])->render();
                    })
                    ->addColumn('action', function ($subCategories) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'edit_route' => route('admin.sub-category.edit', encryptId($subCategories->id)),
                            'delete_id' => $subCategories->id,
                            'current_status' => $subCategories->status,
                            'current_is_main_priority_status' => $subCategories->is_main,
                            'hidden_id' => $subCategories->id,
                        ];
                        return view('admin.render-view.datable-action', [
                            'action_array' => $action_array
                        ])->render();
                    })
                    ->addColumn('image', function ($subCategories) {
                        $baseAWSImageUrl = Storage::disk('s3')->url('');
                        $imageUrl = $baseAWSImageUrl . $subCategories->image;
                        return '<img src="' . $imageUrl . '" style="max-width:100px;" alt="Sub category Image" />';

                    })
                    ->rawColumns(['action', 'image', 'status', 'is_main'])
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
                'category_id' => 'required',
                'name' => [
                    'required',
                    $id == 0 ? 'unique:sub_categories,name' : 'unique:sub_categories,name,' . $id . ',id',
                ],
                'image' => $id == 0 ? 'required|image|mimes:jpeg,png,jpg,gif,svg' : 'image|mimes:jpeg,png,jpg,gif,svg',
            ];

            $validateMessage = [
                'category_id.required' => 'The category is required.',
                'name.required' => 'The sub category name is required.',
                'name.unique' => 'The sub category name has already been taken.',
                'image.required' => 'The image is required.',
                'image.image' => 'The file must be an image.',
                'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, svg.',
            ];


            $validator = Validator::make($request_all, $validateArray, $validateMessage);
            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
            }

            if ($id == 0) {
                if ($request->hasFile('image')) {
                    $image = ImageUploadHelper::uploadSubcategoryImageS3($request->image);
                    $imageSizeInBytes = $request->file('image')->getSize();
                    if (isset($imageSizeInBytes)) {
                        if ($imageSizeInBytes >= 1024 * 1024) {
                            $imageSize = round($imageSizeInBytes / (1024 * 1024), 2);
                            $imageSizeFormatted = $imageSize . ' MB';
                        } else {
                            $imageSize = round($imageSizeInBytes / 1024, 2);
                            $imageSizeFormatted = $imageSize . ' KB';
                        }
                    }
                }

                SubCategory::create([
                    'category_id' => $request->category_id,
                    'name' => $request->name,
                    'description' => $request->description,
                    'image_size' => $imageSizeFormatted,
                    'image' => $image,
                    'is_main' => (int) $request->is_main,
                    'status' => (int) $request->status,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => trans('admin_string.sub_category_added_successfully')
                ]);
            } else {

                $subCategory = SubCategory::where('id', $id)->first();

                if ($request->hasFile('image')) {
                    $filePath = $subCategory->image;
                    if (Storage::disk('s3')->exists($filePath)) {
                        Storage::disk('s3')->delete($filePath);
                    }

                    $image = ImageUploadHelper::uploadSubcategoryImageS3($request->image);
                    $imageSizeInBytes = $request->file('image')->getSize();
                    if (isset($imageSizeInBytes)) {
                        if ($imageSizeInBytes >= 1024 * 1024) {
                            $imageSize = round($imageSizeInBytes / (1024 * 1024), 2);
                            $imageSizeFormatted = $imageSize . ' MB';
                        } else {
                            $imageSize = round($imageSizeInBytes / 1024, 2);
                            $imageSizeFormatted = $imageSize . ' KB';
                        }
                    }
                } else {
                    $image = $subCategory->image;
                    $imageSizeFormatted = $subCategory->image_size;
                }



                SubCategory::where('id', $id)->update([
                    'category_id' => $request->category_id,
                    'name' => $request->name,
                    'description' => $request->description,
                    'image_size' => $imageSizeFormatted,
                    'image' => $image,
                    'is_main' => (int) $request->is_main,
                    'status' => (int) $request->status,
                ]);
                return response()->json([
                    'success' => true,
                    'message' => trans('admin_string.sub_category_updated_successfully')
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
            SubCategory::where('id', $id)->update(['status' => $status]);
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
            SubCategory::where('id', $id)->update(['is_main' => $status]);
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
            $subCategory = SubCategory::where('id', $id)->first();
            if ($subCategory) {
                $filePath = $subCategory->image;
                if (Storage::disk('s3')->exists($filePath)) {
                    Storage::disk('s3')->delete($filePath);
                }

                $subCategory->delete();

                return response()->json([
                    'message' => trans('admin_string.sub_category_deleted_successfully')
                ]);
            } else {
                logger()->error("$function_name: Failed to delete the image from S3 or no sub category found.");
                return response()->json(['error' => 'Failed to delete the image from S3 or no sub category found.'], 500);
            }

        } catch (\Exception $e) {
            logger()->error("$function_name: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }


}
