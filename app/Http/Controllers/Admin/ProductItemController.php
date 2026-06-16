<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductItem;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use App\Models\ProductMedia;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ProductItemController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/ProductItemController";
    }

    public function index()
    {
        try {
            return view('admin.product-item.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'index');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        try {
            $brands = ProductBrand::where('status', 1)->get();
            $categories = ProductCategory::where('status', 1)->get();
            return view('admin.product-item.create', compact('brands', 'categories'));
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'create');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function show($id)
    {
        try {
            $product = ProductItem::with(['media', 'variants'])->where('id', decryptId($id))->first();
            if (!$product) {
                return redirect()->route('admin.product-item.index')->with('error', 'Product not found');
            }
            return view('admin.product-item.show', compact('product'));
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'show');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        try {
            $product = ProductItem::with(['media', 'variants'])->where('id', decryptId($id))->first();
            $brands = ProductBrand::where('status', 1)->get();
            $categories = ProductCategory::where('status', 1)->get();
            $subcategories = ProductSubCategory::where('category_id', $product->category_id)->where('status', 1)->get();
            
            if ($product) {
                return view('admin.product-item.edit', compact('product', 'brands', 'categories', 'subcategories'));
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'edit');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataProductItem(Request $request)
    {
        try {
            if ($request->ajax()) {
                $products = DB::table('product_items')
                    ->leftJoin('product_brands as pb', 'pb.id', '=', 'product_items.brand_id')
                    ->leftJoin('product_categories as pc', 'pc.id', '=', 'product_items.category_id')
                    ->select('product_items.*', 'pb.name as brand_name', 'pc.name as category_name');

                if ($request->status !== null && $request->status !== '') {
                    $products->where('product_items.status', $request->status);
                }

                if ($request->category_id !== null && $request->category_id !== '') {
                    $products->where('product_items.category_id', $request->category_id);
                }

                return DataTables::of($products)
                    ->addColumn('status', function ($products) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => $products->status
                        ];
                        return view('admin.render-view.datable-label', compact('status_array'))->render();
                    })
                    ->addColumn('action', function ($products) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'is_view_action' => 1,
                            'view_route' => route('admin.product-item.show', encryptId($products->id)),
                            'edit_route' => route('admin.product-item.edit', encryptId($products->id)),
                            'delete_id' => $products->id,
                            'current_status' => $products->status,
                            'hidden_id' => $products->id,
                        ];
                        return view('admin.render-view.datable-action', compact('action_array'))->render();
                    })
                    ->rawColumns(['action', 'status'])
                    ->make(true);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'getDataProductItem');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function store(Request $request)
    {
        $id = $request->input('edit_value', 0);
        $rules = [
            'name'        => 'required|string|max:255|unique:product_items,name,' . $id,
            'price'       => 'required|numeric',
            'category_id' => 'required|exists:product_categories,id',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
        }

        try {
            // Handle content_json - form sends it as array, needs to be JSON string for DB
            $contentJson = $request->content_json;
            if (is_array($contentJson)) {
                // Filter out empty sections
                $contentJson = array_values(array_filter($contentJson, function($section) {
                    return !empty($section['type']);
                }));
            } else {
                $contentJson = null;
            }

            $data = [
                'brand_id'           => $request->brand_id ?: null,
                'category_id'        => $request->category_id,
                'sub_category_id'    => $request->sub_category_id ?: null,
                'name'               => $request->name,
                'slug'               => Str::slug($request->name),
                'short_description'  => $request->short_description,
                'description'        => $request->description,
                'price'              => $request->price,
                'discount_percentage'=> $request->discount_percentage ?? 0,
                'sku'                => $request->sku,
                'unit'               => $request->unit,
                'stock_quantity'     => $request->stock_quantity ?? 0,
                'is_featured'        => $request->has('is_featured') ? 1 : 0,
                'is_new'             => $request->has('is_new') ? 1 : 0,
                'show_in_client_app' => $request->has('show_in_client_app') ? 1 : 0,
                'status'             => (int) $request->status,
                'content_json'       => $contentJson,
            ];

            if ($id == 0) {
                $product = ProductItem::create($data);
                $msg = "Product added successfully";
            } else {
                $product = ProductItem::find($id);
                if (!$product) {
                    return response()->json(['message' => 'Product not found'], 404);
                }
                $product->update($data);
                $msg = "Product updated successfully";
            }

            // Handle Media (Images/Videos)
            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    $filename = time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/product-media'), $filename);
                    
                    $extension = strtolower($file->getClientOriginalExtension());
                    $type = in_array($extension, ['mp4', 'mov', 'avi', 'webm']) ? 'video' : 'image';

                    ProductMedia::create([
                        'product_id' => $product->id,
                        'type'       => $type,
                        'file_path'  => $filename,
                        'is_main'    => 0,
                        'status'     => 1
                    ]);
                }
            }

            // Handle Variants — replace all on update
            if ($request->variants && is_array($request->variants)) {
                ProductVariant::where('product_id', $product->id)->delete();
                foreach ($request->variants as $variant) {
                    if (!is_array($variant)) continue;
                    $variantName = isset($variant['name']) ? trim($variant['name']) : '';
                    if (empty($variantName)) continue;
                    ProductVariant::create([
                        'product_id'          => $product->id,
                        'variant_name'        => $variantName,
                        'price'               => isset($variant['price']) && $variant['price'] !== '' ? $variant['price'] : null,
                        'discount_percentage' => isset($variant['discount_percentage']) && $variant['discount_percentage'] !== '' ? $variant['discount_percentage'] : null,
                        'stock_quantity'      => isset($variant['stock_quantity']) ? (int) $variant['stock_quantity'] : 0,
                        'status'              => 1
                    ]);
                }
            }

            return response()->json(['success' => true, 'message' => $msg]);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'store');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function destroy($id)
    {
        try {
            $product = ProductItem::find($id);
            if ($product) {
                // Delete media files
                $media = ProductMedia::where('product_id', $product->id)->get();
                foreach ($media as $m) {
                    if (File::exists(public_path('uploads/product-media/' . $m->file_path))) {
                        File::delete(public_path('uploads/product-media/' . $m->file_path));
                    }
                    $m->delete();
                }
                
                ProductVariant::where('product_id', $product->id)->delete();
                $product->delete();
                
                return response()->json(['message' => 'Product deleted successfully']);
            }
            return response()->json(['error' => 'Product not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], 500);
        }
    }

    public function getSubcategories($categoryId)
    {
        return response()->json(ProductSubCategory::where('category_id', $categoryId)->where('status', 1)->get());
    }

    public function changeStatus($id, $status)
    {
        try {
            ProductItem::where('id', $id)->update(['status' => $status]);
            return response()->json(['message' => 'Status changed successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], 500);
        }
    }
}
