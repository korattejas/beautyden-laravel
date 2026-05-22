<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Exception;

class ProductController extends Controller
{
    protected mixed $success_status, $exception_status, $backend_error_status, $validation_error_status, $common_error_message;
    protected string $controller_name;

    public function __construct()
    {
        $this->controller_name = 'API/ProductController';
        $this->success_status = config('custom.status_code_for_success');
        $this->exception_status = config('custom.status_code_for_exception_error');
        $this->backend_error_status = config('custom.status_code_for_backend_error');
        $this->validation_error_status = config('custom.status_code_for_validation_error');
        $this->common_error_message = config('custom.common_error_message');
    }

    /**
     * Get Product Categories with Subcategories
     */
    public function getProductCategories(): JsonResponse
    {
        $function_name = 'getProductCategories';

        try {
            $categories = DB::table('product_categories as c')
                ->select(
                    'c.id',
                    'c.name',
                    DB::raw('CONCAT("' . asset('uploads/product-category') . '/", c.image) AS image'),
                    DB::raw('IF(c.image LIKE "%.mp4" OR c.image LIKE "%.mov" OR c.image LIKE "%.avi" OR c.image LIKE "%.wmv", "video", "image") AS image_type'),
                    'c.is_featured',
                    'c.is_new'
                )
                ->where('c.status', 1)
                ->get();

            if ($categories->isEmpty()) {
                return $this->sendError('No category found.', $this->backend_error_status);
            }

            $subCategories = DB::table('product_sub_categories as sc')
                ->select(
                    'sc.id',
                    'sc.category_id',
                    'sc.name',
                    'sc.is_featured',
                    'sc.is_new'
                )
                ->where('sc.status', 1)
                ->get()
                ->groupBy('category_id');

            $categories->transform(function ($category) use ($subCategories) {
                $category->is_featured = (int) $category->is_featured;
                $category->is_new = (int) $category->is_new;
                $category->subcategories = $subCategories[$category->id] ?? collect();
                return $category;
            });

            return $this->sendResponse(
                $categories,
                'Product categories retrieved successfully',
                $this->success_status
            );

        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    /**
     * Get Products with Filters and Pagination
     */
    public function getProducts(Request $request): JsonResponse
    {
        $function_name = 'getProducts';

        try {
            $query = DB::table('product_items as p')
                ->leftJoin('product_brands as b', 'p.brand_id', '=', 'b.id')
                ->leftJoin('product_categories as c', 'p.category_id', '=', 'c.id')
                ->select(
                    'p.id',
                    'p.name',
                    'p.brand_id',
                    'b.name as brand_name',
                    'p.category_id',
                    'c.name as category_name',
                    'p.sub_category_id',
                    'p.price',
                    'p.discount_percentage',
                    DB::raw('p.price - (p.price * p.discount_percentage / 100) as sale_price'),
                    'p.sku',
                    'p.stock_quantity',
                    'p.is_featured',
                    'p.is_new',
                    'p.short_description'
                )
                ->where('p.status', 1)
                ->where('p.show_in_client_app', 1); // Only show products marked for client app

            // Filters
            if ($request->filled('category_id')) {
                $query->where('p.category_id', $request->category_id);
            }

            if ($request->filled('sub_category_id')) {
                $query->where('p.sub_category_id', $request->sub_category_id);
            }

            if ($request->filled('brand_id')) {
                $query->where('p.brand_id', $request->brand_id);
            }

            if ($request->filled('is_featured')) {
                $query->where('p.is_featured', $request->is_featured);
            }

            if ($request->filled('is_new')) {
                $query->where('p.is_new', $request->is_new);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('p.name', 'like', "%$search%")
                        ->orWhere('p.short_description', 'like', "%$search%")
                        ->orWhere('b.name', 'like', "%$search%");
                });
            }

            $perPage = $request->per_page ?? 20;
            $page = $request->page ?? 1;

            $products = $query->orderByDesc('p.id')
                ->paginate($perPage, ['*'], 'page', $page);

            // Append Main Image to each product
            $products->getCollection()->transform(function ($product) {
                $mainImage = DB::table('product_media')
                    ->where('product_id', $product->id)
                    ->where('type', 'image')
                    ->orderBy('is_main', 'desc')
                    ->orderBy('id', 'asc')
                    ->value('file_path');

                $product->main_image = $mainImage ? asset('uploads/product-media/' . $mainImage) : null;
                return $product;
            });

            if ($products->total() === 0) {
                return $this->sendError('No product found.', $this->backend_error_status);
            }

            return $this->sendResponse(
                $products,
                'Products retrieved successfully',
                $this->success_status
            );

        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    /**
     * Get Product Details
     */
    public function getProductDetails($id): JsonResponse
    {
        $function_name = 'getProductDetails';

        try {
            $product = DB::table('product_items as p')
                ->leftJoin('product_brands as b', 'p.brand_id', '=', 'b.id')
                ->leftJoin('product_categories as c', 'p.category_id', '=', 'c.id')
                ->select(
                    'p.*',
                    'b.name as brand_name',
                    'c.name as category_name',
                    DB::raw('p.price - (p.price * p.discount_percentage / 100) as sale_price')
                )
                ->where('p.id', $id)
                ->where('p.status', 1)
                ->first();

            if (!$product) {
                return $this->sendError('Product not found.', $this->backend_error_status);
            }

            // Decode content_json
            $product->content_json = $product->content_json ? json_decode($product->content_json, true) : [];

            // Get Media
            $media = DB::table('product_media')
                ->where('product_id', $id)
                ->where('status', 1)
                ->select('id', 'type', 'file_path', 'is_main')
                ->get()
                ->map(function ($m) {
                    $m->file_path = asset('uploads/product-media/' . $m->file_path);
                    return $m;
                });

            $product->media = $media;

            // Get Variants
            $variants = DB::table('product_variants')
                ->where('product_id', $id)
                ->where('status', 1)
                ->select('id', 'variant_name', 'price', 'discount_percentage', 'stock_quantity')
                ->get();

            $product->variants = $variants;

            return $this->sendResponse(
                $product,
                'Product details retrieved successfully',
                $this->success_status
            );

        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }
}
