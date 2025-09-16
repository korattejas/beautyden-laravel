<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductBrand;
use App\Helpers\ImageUploadHelper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ProductBrandController extends Controller
{
   protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/ProductBrandController";
    }
    public function index()
    {
        $function_name = 'index';
        try {
            return view('admin.product-brand.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        $function_name = 'create';
        try {
            return view('admin.product-brand.create');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        $function_name = 'edit';
        try {
            $category = ProductBrand::where('id', decryptId($id))->first();
            if ($category) {
                return view('admin.product-brand.edit', [
                    'category' => $category
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
        $request_all = request()->all();
        try {
            $id = $request->input('edit_value');
            $validateArray = [
                'name' => [
                    'required',
                    $id == 0 ? 'unique:service_categories,name' : 'unique:service_categories,name,' . $id . ',id',
                ],
                'icon' => $id == 0 ? 'image|mimes:jpeg,png,jpg,gif,svg,webp' : 'image|mimes:jpeg,png,jpg,gif,svg,webp',
            ];

            $validateMessage = [
                'name.required' => 'The category name is required.',
                'name.unique' => 'The category name has already been taken.',
                'icon.image' => 'The file must be an image.',
                'icon.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, svg, webp.',
            ];


            $validator = Validator::make($request_all, $validateArray, $validateMessage);
            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
            }


            $photoFilename = null;
            if ($request->hasFile('icon')) {
                $team = ProductBrand::where('id',$id)->first();
                if ($team) {
                    $filePath = public_path('uploads/product-brand/' . $team->icon);
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                }
                $photoFilename = ImageUploadHelper::ProductBrandImageUpload($request->file('icon'));
            } elseif ($id != 0) {
                $photoFilename = ProductBrand::find($id)?->icon;
            }
            $data = [
                'name' => $request->name,
                 'description' => $request->description,
                'icon' => $photoFilename ?? null,
                'status' => (int) $request->input('status', 1),
            ];

            if ($id == 0) {
                ProductBrand::create($data);
                $msg = 'Product Brand added successfully';
            } else {
                ProductBrand::where('id', $id)->update($data);
                $msg = 'Product Brand updated successfully';
            }

            return response()->json(['success' => true, 'message' => $msg]);
        } catch (\Exception $e) {
            logger()->error("store: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

         public function getDataProductBrand(Request $request)
    {
        $function_name = 'getDataProductBrand';
        try {
            if ($request->ajax()) {
                $product_brands = DB::table('product_brands')->select('product_brands.*');
                return DataTables::of($product_brands)
                    ->addColumn('status', function ($product_brands) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => $product_brands->status
                        ];
                        return view('admin.render-view.datable-label', [
                            'status_array' => $status_array
                        ])->render();
                    })
                    
                    ->addColumn('action', function ($product_brands) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'edit_route' => route('admin.product-brand.edit', encryptId($product_brands->id)),
                            'delete_id' => $product_brands->id,
                            'current_status' => $product_brands->status,
                            'hidden_id' => $product_brands->id,
                        ];
                        return view('admin.render-view.datable-action', [
                            'action_array' => $action_array
                        ])->render();
                    })
                    ->addColumn('icon', function ($product_brands) {
                        if ($product_brands->icon && file_exists(public_path('uploads/product-brand/' . $product_brands->icon))) {
                            $imageUrl = asset('uploads/product-brand/' . $product_brands->icon);
                            return '<img src="' . $imageUrl . '" style="max-width:100px;" alt="Team Icon" />';
                        }
                        return '';
                    })

                    ->rawColumns(['action', 'icon', 'status'])
                    ->make(true);
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
            ProductBrand::where('id', $id)->update(['status' => $status]);
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
            $ProductBrand = ProductBrand::where('id', $id)->first();
            if ($ProductBrand) {
                $filePath = public_path('uploads/product-brand/' . $ProductBrand->icon);

                if (File::exists($filePath)) {
                    File::delete($filePath);
                }

                $ProductBrand->delete();

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
