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
                    DB::raw('CONCAT("' . asset('uploads/product-category') . '/", c.image) AS category_image'),
                    'p.sub_category_id',
                    'p.price',
                    'p.discount_percentage',
                    DB::raw('p.price - (p.price * p.discount_percentage / 100) as sale_price'),
                    'p.sku',
                    'p.unit',
                    'p.stock_quantity',
                    'p.is_featured',
                    'p.is_new',
                    'p.short_description'
                )
                ->where('p.status', 1); // Only show products marked for client app

            // Filters
            if ($request->filled('category_id')) {
                $categoryIds = is_array($request->category_id) ? $request->category_id : explode(',', $request->category_id);
                $query->whereIn('p.category_id', $categoryIds);
            }

            if ($request->filled('sub_category_id')) {
                $subCategoryIds = is_array($request->sub_category_id) ? $request->sub_category_id : explode(',', $request->sub_category_id);
                $query->whereIn('p.sub_category_id', $subCategoryIds);
            }

            if ($request->filled('brand_id')) {
                $brandIds = is_array($request->brand_id) ? $request->brand_id : explode(',', $request->brand_id);
                $query->whereIn('p.brand_id', $brandIds);
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

            // Sorting
            if ($request->filled('sort_by')) {
                if ($request->sort_by === 'price_low_high') {
                    $query->orderBy('sale_price', 'asc');
                } elseif ($request->sort_by === 'price_high_low') {
                    $query->orderBy('sale_price', 'desc');
                } elseif ($request->sort_by === 'newest') {
                    $query->where('p.is_new', 1)->orderByDesc('p.id');
                } else {
                    $query->orderByDesc('p.id');
                }
            } else {
                $query->orderByDesc('p.id');
            }

            $products = $query->paginate($perPage, ['*'], 'page', $page);

            // Append Main Image, All Media, and Variants to each product
            $products->getCollection()->transform(function ($product) {
                // Get All Media
                $media = DB::table('product_media')
                    ->where('product_id', $product->id)
                    ->where('status', 1)
                    ->select('id', 'type', 'file_path', 'is_main')
                    ->orderBy('is_main', 'desc')
                    ->orderBy('id', 'asc')
                    ->get()
                    ->map(function ($m) {
                        $m->file_path = asset('uploads/product-media/' . $m->file_path);
                        return $m;
                    });

                // Set Main Image and All Media
                $mainImageObj = $media->where('type', 'image')->first();
                $product->main_image = $mainImageObj ? $mainImageObj->file_path : null;
                $product->media = $media->values();

                // Get Variants
                $variants = DB::table('product_variants')
                    ->where('product_id', $product->id)
                    ->where('status', 1)
                    ->select('id', 'variant_name', 'price', 'discount_percentage', 'stock_quantity')
                    ->get();

                $product->variants = $variants;

                return $product;
            });

            $responseData = $products->toArray();

            $responseData['filters'] = [
                'categories' => DB::table('product_categories')->select('id', 'name', DB::raw('CONCAT("' . asset('uploads/product-category') . '/", image) AS image'))->where('status', 1)->get(),
                'sub_categories' => DB::table('product_sub_categories')->select('id', 'name', 'category_id')->where('status', 1)->get(),
                'brands' => DB::table('product_brands')->select('id', 'name')->where('status', 1)->get(),
                'sort_options' => [
                    ['id' => 'newest', 'name' => 'Newest Arrivals'],
                    ['id' => 'price_low_high', 'name' => 'Price: Low to High'],
                    ['id' => 'price_high_low', 'name' => 'Price: High to Low']
                ]
            ];

            return $this->sendResponse(
                $responseData,
                $products->total() > 0 ? 'Products retrieved successfully' : 'No products found.',
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
                    DB::raw('CONCAT("' . asset('uploads/product-category') . '/", c.image) AS category_image'),
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

    /**
     * Place an Order
     */
    public function placeOrder(Request $request): JsonResponse
    {
        $function_name = 'placeOrder';

        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|integer|exists:product_items,id',
                'items.*.variant_id' => 'nullable|integer',
                'items.*.qty' => 'required|integer|min:1',
                'address' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            DB::beginTransaction();

            $total_amount = 0;
            $order_data = [];
            $stock_updates = [];

            foreach ($request->items as $item) {
                $product = DB::table('product_items')->where('id', $item['product_id'])->where('status', 1)->lockForUpdate()->first();
                if (!$product) {
                    DB::rollBack();
                    return $this->sendError('Product not found or inactive: ' . $item['product_id'], $this->validation_error_status);
                }

                $price = $product->price;
                $discount_percentage = $product->discount_percentage;
                $variantName = '';

                if (!empty($item['variant_id'])) {
                    $variant = DB::table('product_variants')->where('id', $item['variant_id'])->where('product_id', $item['product_id'])->where('status', 1)->lockForUpdate()->first();
                    if ($variant) {
                        if ($variant->stock_quantity < $item['qty']) {
                            DB::rollBack();
                            return $this->sendError('Out of stock for variant: ' . $variant->variant_name . ' of product: ' . $product->name, $this->validation_error_status);
                        }

                        $price = $variant->price;
                        $discount_percentage = $variant->discount_percentage;
                        $variantName = $variant->variant_name;

                        $stock_updates[] = [
                            'table' => 'product_variants',
                            'id' => $variant->id,
                            'qty' => $item['qty']
                        ];
                    } else {
                        DB::rollBack();
                        return $this->sendError('Invalid variant ID: ' . $item['variant_id'] . ' for product: ' . $product->name, $this->validation_error_status);
                    }
                } else {
                    if ($product->stock_quantity < $item['qty']) {
                        DB::rollBack();
                        return $this->sendError('Out of stock for product: ' . $product->name, $this->validation_error_status);
                    }

                    $stock_updates[] = [
                        'table' => 'product_items',
                        'id' => $product->id,
                        'qty' => $item['qty']
                    ];
                }

                $salePrice = $price - ($price * $discount_percentage / 100);
                $itemTotal = $salePrice * $item['qty'];
                $total_amount += $itemTotal;

                $order_data[] = [
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'] ?? null,
                    'name' => $product->name,
                    'variant_name' => $variantName,
                    'qty' => $item['qty'],
                    'price' => round($salePrice, 2),
                    'total' => round($itemTotal, 2),
                ];
            }

            // Get authenticated user ID (Beautician)
            $user = auth()->guard('user')->user();
            $userId = $user ? $user->id : null;

            // Create Order
            $order = \App\Models\ProductOrder::create([
                'user_id' => $userId,
                'total_amount' => round($total_amount, 2),
                'payment_status' => 'Pending',
                'order_status' => 'Pending',
                'address' => $request->address,
                'order_data' => $order_data,
                'status' => 1,
            ]);

            // Deduct Stock
            foreach ($stock_updates as $update) {
                DB::table($update['table'])->where('id', $update['id'])->decrement('stock_quantity', $update['qty']);
            }

            DB::commit();

            $responseData = [
                'order_id' => $order->id,
                'total_amount' => $order->total_amount,
                'items' => $order->order_data,
                'order_status' => $order->order_status,
                'payment_status' => $order->payment_status
            ];

            return $this->sendResponse($responseData, 'Order placed successfully.', $this->success_status);

        } catch (Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    /**
     * Get Beautician's Orders
     */
    public function getMyOrders(Request $request): JsonResponse
    {
        $function_name = 'getMyOrders';

        try {
            $user = auth()->guard('user')->user();
            if (!$user) {
                return $this->sendError('Unauthenticated', 401);
            }

            $orders = \App\Models\ProductOrder::where('user_id', $user->id)
                        ->orderBy('id', 'desc')
                        ->get();

            $productIds = [];
            foreach ($orders as $order) {
                $items = is_string($order->order_data) ? json_decode($order->order_data, true) : $order->order_data;
                if (is_array($items)) {
                    foreach ($items as $item) {
                        if (isset($item['product_id'])) {
                            $productIds[] = $item['product_id'];
                        }
                    }
                }
            }
            $productIds = array_unique($productIds);

            $media = DB::table('product_media')
                ->whereIn('product_id', $productIds)
                ->where('type', 'image')
                ->where('status', 1)
                ->orderBy('is_main', 'desc')
                ->orderBy('id', 'asc')
                ->get()
                ->groupBy('product_id');

            $productImages = [];
            foreach ($media as $productId => $productMediaList) {
                $firstMedia = $productMediaList->first();
                $productImages[$productId] = asset('uploads/product-media/' . $firstMedia->file_path);
            }

            $formattedOrders = $orders->map(function ($order) use ($productImages) {
                $totalProducts = 0;
                $items = is_string($order->order_data) ? json_decode($order->order_data, true) : $order->order_data;
                
                if (is_array($items)) {
                    foreach ($items as &$item) {
                        $totalProducts += $item['qty'] ?? 1;
                        if (isset($item['product_id'])) {
                            $item['product_image'] = $productImages[$item['product_id']] ?? null;
                        } else {
                            $item['product_image'] = null;
                        }
                    }
                }
                
                return [
                    'id' => $order->id,
                    'order_number' => 'BDPROD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
                    'total_amount' => $order->total_amount,
                    'total_products' => $totalProducts,
                    'payment_status' => $order->payment_status,
                    'order_status' => $order->order_status,
                    'date' => $order->created_at ? $order->created_at->timezone('Asia/Kolkata')->format('d-M-Y h:i A') : '',
                    'items' => $items,
                ];
            });

            return $this->sendResponse($formattedOrders, 'Orders retrieved successfully.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    /**
     * Get Single Order Details
     */
    public function getOrderDetails(Request $request): JsonResponse
    {
        $function_name = 'getOrderDetails';

        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'order_id' => 'required|integer|exists:product_orders,id',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            $user = auth()->guard('user')->user();
            if (!$user) {
                return $this->sendError('Unauthenticated', 401);
            }

            $order = \App\Models\ProductOrder::where('id', $request->order_id)
                        ->where('user_id', $user->id)
                        ->first();

            if (!$order) {
                return $this->sendError('Order not found or access denied.', 404);
            }

            $totalProducts = 0;
            $items = is_string($order->order_data) ? json_decode($order->order_data, true) : $order->order_data;
            if (is_array($items)) {
                $productIds = [];
                foreach ($items as $item) {
                    if (isset($item['product_id'])) {
                        $productIds[] = $item['product_id'];
                    }
                }
                $productIds = array_unique($productIds);

                $media = DB::table('product_media')
                    ->whereIn('product_id', $productIds)
                    ->where('type', 'image')
                    ->where('status', 1)
                    ->orderBy('is_main', 'desc')
                    ->orderBy('id', 'asc')
                    ->get()
                    ->groupBy('product_id');

                $productImages = [];
                foreach ($media as $productId => $productMediaList) {
                    $firstMedia = $productMediaList->first();
                    $productImages[$productId] = asset('uploads/product-media/' . $firstMedia->file_path);
                }

                foreach ($items as &$item) {
                    $totalProducts += $item['qty'] ?? 1;
                    if (isset($item['product_id'])) {
                        $item['product_image'] = $productImages[$item['product_id']] ?? null;
                    } else {
                        $item['product_image'] = null;
                    }
                }
            }

            $data = [
                'id' => $order->id,
                'order_number' => 'BDPROD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
                'total_amount' => $order->total_amount,
                'total_products' => $totalProducts,
                'payment_status' => $order->payment_status,
                'order_status' => $order->order_status,
                'date' => $order->created_at ? $order->created_at->timezone('Asia/Kolkata')->format('d-M-Y h:i A') : '',
                'address' => $order->address ?? null,
                'items' => $items,
            ];

            return $this->sendResponse($data, 'Order details retrieved successfully.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }


    /**
     * Export Order Invoice
     */
    public function exportOrderInvoice(Request $request): JsonResponse
    {
        $function_name = 'exportOrderInvoice';
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'order_id' => 'required|integer|exists:product_orders,id',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            $user = auth()->guard('user')->user();
            if (!$user) {
                return $this->sendError('Unauthenticated', 401);
            }

            $order = \App\Models\ProductOrder::where('id', $request->order_id)
                        ->where('user_id', $user->id)
                        ->first();

            if (!$order) {
                return $this->sendError('Order not found or access denied.', 404);
            }

            // Get TeamMember info for the invoice
            $phone = preg_replace('/\D/', '', $user->mobile_number);
            $teamMember = \App\Models\TeamMember::whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, '+', ''), '-', ''), ' ', ''), '(', '') LIKE '%$phone%'")
                ->orWhere('phone', $user->mobile_number)
                ->first();

            $orderNumber = 'BDPROD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT);
            // Use same filename to overwrite and save server space
            $fileName = 'invoice_' . $orderNumber . '.pdf';
            $directory = public_path('uploads/exports');

            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.product_orders.pdf', compact('order', 'orderNumber', 'teamMember', 'user'));
            $pdf->save($directory . '/' . $fileName);

            // Append time as query param to bypass cache, but keep the same file on server
            $fileUrl = asset('uploads/exports/' . $fileName) . '?v=' . time();

            return $this->sendResponse([
                'file_url' => $fileUrl
            ], 'Invoice generated successfully.', $this->success_status);

        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }
}
