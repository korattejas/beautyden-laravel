<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductSubCategory;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ProductSubCategoryController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/ProductSubCategoryController";
    }

    public function index()
    {
        try {
            return view('admin.product-subcategory.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'index');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        try {
            $categories = ProductCategory::where('status', 1)->get();
            return view('admin.product-subcategory.create', compact('categories'));
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'create');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        try {
            $subcategory = ProductSubCategory::where('id', decryptId($id))->first();
            $categories = ProductCategory::where('status', 1)->get();
            if ($subcategory) {
                return view('admin.product-subcategory.edit', compact('subcategory', 'categories'));
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'edit');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataProductSubCategory(Request $request)
    {
        try {
            if ($request->ajax()) {
                $subcategories = DB::table('product_sub_categories')
                    ->leftJoin('product_categories as pc', 'pc.id', '=', 'product_sub_categories.category_id')
                    ->select('product_sub_categories.*', 'pc.name as category_name');

                if ($request->status !== null && $request->status !== '') {
                    $subcategories->where('product_sub_categories.status', $request->status);
                }

                if ($request->category_id !== null && $request->category_id !== '') {
                    $subcategories->where('product_sub_categories.category_id', $request->category_id);
                }

                return DataTables::of($subcategories)
                    ->addColumn('status', function ($subcategories) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => $subcategories->status
                        ];
                        return view('admin.render-view.datable-label', compact('status_array'))->render();
                    })
                    ->addColumn('is_featured', function ($subcategories) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => 3,
                            'current_is_popular_priority_status' => $subcategories->is_featured
                        ];
                        return view('admin.render-view.datable-label', compact('status_array'))->render();
                    })
                    ->addColumn('is_new', function ($subcategories) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => 4,
                            'current_is_new_priority_status' => $subcategories->is_new
                        ];
                        return view('admin.render-view.datable-label', compact('status_array'))->render();
                    })
                    ->addColumn('action', function ($subcategories) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'edit_route' => route('admin.product-subcategory.edit', encryptId($subcategories->id)),
                            'delete_id' => $subcategories->id,
                            'current_status' => $subcategories->status,
                            'current_is_popular_priority_status' => $subcategories->is_featured,
                            'current_is_new_status' => $subcategories->is_new,
                            'hidden_id' => $subcategories->id,
                        ];
                        return view('admin.render-view.datable-action', compact('action_array'))->render();
                    })
                    ->rawColumns(['action', 'status', 'is_featured', 'is_new'])
                    ->make(true);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'getDataProductSubCategory');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function store(Request $request)
    {
        $id = $request->input('edit_value', 0);
        $rules = [
            'category_id' => 'required|exists:product_categories,id',
            'name' => 'required|string|max:255|unique:product_sub_categories,name,' . $id . ',id,category_id,' . $request->category_id,
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
        }

        try {
            $data = [
                'category_id' => $request->category_id,
                'name' => $request->name,
                'is_featured' => (int) $request->is_featured,
                'is_new' => (int) $request->is_new,
                'status' => (int) $request->status,
            ];

            if ($id == 0) {
                ProductSubCategory::create($data);
                $msg = "Product sub-category added successfully";
            } else {
                ProductSubCategory::where('id', $id)->update($data);
                $msg = "Product sub-category updated successfully";
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
            ProductSubCategory::where('id', $id)->update(['status' => $status]);
            return response()->json(['message' => 'Status changed successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $subcategory = ProductSubCategory::find($id);
            if ($subcategory) {
                $subcategory->delete();
                return response()->json(['message' => 'Sub-category deleted successfully']);
            }
            return response()->json(['error' => 'Sub-category not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], 500);
        }
    }
}
