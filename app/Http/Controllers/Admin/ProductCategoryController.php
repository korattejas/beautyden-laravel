<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Helpers\ImageUploadHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;

class ProductCategoryController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/ProductCategoryController";
    }

    public function index()
    {
        try {
            return view('admin.product-category.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'index');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        try {
            return view('admin.product-category.create');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'create');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        try {
            $category = ProductCategory::where('id', decryptId($id))->first();
            if ($category) {
                return view('admin.product-category.edit', compact('category'));
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'edit');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataProductCategory(Request $request)
    {
        try {
            if ($request->ajax()) {
                $categories = DB::table('product_categories')->select('product_categories.*');

                if ($request->status !== null && $request->status !== '') {
                    $categories->where('product_categories.status', $request->status);
                }

                if ($request->is_featured !== null && $request->is_featured !== '') {
                    $categories->where('product_categories.is_featured', $request->is_featured);
                }

                if ($request->is_new !== null && $request->is_new !== '') {
                    $categories->where('product_categories.is_new', $request->is_new);
                }

                return DataTables::of($categories)
                    ->addColumn('status', function ($categories) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => $categories->status
                        ];
                        return view('admin.render-view.datable-label', compact('status_array'))->render();
                    })
                    ->addColumn('is_featured', function ($categories) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => 3, // Assuming 3 is for featured/popular in their system
                            'current_is_popular_priority_status' => $categories->is_featured
                        ];
                        return view('admin.render-view.datable-label', compact('status_array'))->render();
                    })
                    ->addColumn('is_new', function ($categories) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => 4, // Assuming 4 is for is_new in their system
                            'current_is_new_priority_status' => $categories->is_new
                        ];
                        return view('admin.render-view.datable-label', compact('status_array'))->render();
                    })
                    ->addColumn('action', function ($categories) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'edit_route' => route('admin.product-category.edit', encryptId($categories->id)),
                            'delete_id' => $categories->id,
                            'current_status' => $categories->status,
                            'current_is_popular_priority_status' => $categories->is_featured,
                            'current_is_new_status' => $categories->is_new,
                            'hidden_id' => $categories->id,
                        ];
                        return view('admin.render-view.datable-action', compact('action_array'))->render();
                    })
                    ->addColumn('image', function ($categories) {
                        return $categories->image;
                    })
                    ->rawColumns(['action', 'image', 'status', 'is_featured', 'is_new'])
                    ->make(true);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'getDataProductCategory');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function store(Request $request)
    {
        $id = $request->input('edit_value', 0);
        $rules = [
            'name' => 'required|string|max:255|unique:product_categories,name,' . $id,
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
        }

        try {
            $category = $id ? ProductCategory::find($id) : null;
            $image = $category ? $category->image : null;

            if ($request->hasFile('icon')) {
                if ($image && File::exists(public_path('uploads/product-category/' . $image))) {
                    File::delete(public_path('uploads/product-category/' . $image));
                }
                // Assuming a helper method exists or we use standard upload
                $image = time() . '.' . $request->icon->extension();
                $request->icon->move(public_path('uploads/product-category'), $image);
            }

            $data = [
                'name' => $request->name,
                'image' => $image,
                'is_featured' => (int) $request->is_featured,
                'is_new' => (int) $request->is_new,
                'status' => (int) $request->status,
            ];

            if ($id == 0) {
                ProductCategory::create($data);
                $msg = "Product category added successfully";
            } else {
                ProductCategory::where('id', $id)->update($data);
                $msg = "Product category updated successfully";
            }

            return response()->json(['success' => true, 'message' => $msg]);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'store');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function changeStatus($id, $status)
    {
        try {
            ProductCategory::where('id', $id)->update(['status' => $status]);
            return response()->json(['message' => 'Status changed successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $category = ProductCategory::find($id);
            if ($category) {
                if ($category->image && File::exists(public_path('uploads/product-category/' . $category->image))) {
                    File::delete(public_path('uploads/product-category/' . $category->image));
                }
                $category->delete();
                return response()->json(['message' => 'Category deleted successfully']);
            }
            return response()->json(['error' => 'Category not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], 500);
        }
    }
}
