<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Exception;

class BlogController extends Controller
{
    protected mixed $success_status, $exception_status, $backend_error_status, $validation_error_status, $common_error_message;

    protected string $controller_name;

    public function __construct()
    {
        $this->controller_name = 'API/BlogController';
        $this->success_status = config('custom.status_code_for_success');
        $this->exception_status = config('custom.status_code_for_exception_error');
        $this->backend_error_status = config('custom.status_code_for_backend_error');
        $this->validation_error_status = config('custom.status_code_for_validation_error');
        $this->common_error_message = config('custom.common_error_message');
    }

    public function getBlogCategory(): JsonResponse
    {
        $function_name = 'getBlogCategory';
        try {
            $categories = DB::table('blog_categories')->select(
                'id',
                'name',
                DB::raw('CONCAT("' . asset('uploads/blog-category') . '/", icon) AS icon'),
                'description',
                'is_popular'
            )
                ->where('status', 1)
                ->orderBy('is_popular', 'desc')
                ->get();

            if ($categories->isEmpty()) {
                return $this->sendError('No category found.', $this->backend_error_status);
            }

            return $this->sendResponse($categories, 'Categories retrieved successfully', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    public function getBlogs(Request $request): JsonResponse
    {
        $function_name = 'getBlogs';

        try {
            $query = DB::table('blogs as b')
                ->join('blog_categories as c', 'b.category_id', '=', 'c.id')
                ->select(
                    'b.id',
                    'b.category_id',
                    'c.name as category_name',
                    'b.title',
                    'b.slug',
                    'b.excerpt',
                    'b.content',
                    'b.read_time',
                    'b.author',
                    'b.publish_date',
                    'b.tags',
                    DB::raw('CONCAT("' . asset('uploads/blogs') . '/", b.icon) AS icon'),
                    'b.featured',
                )
                ->where('b.status', 1);

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('b.title', 'like', "%{$search}%")
                        ->orWhere('b.excerpt', 'like', "%{$search}%")
                        ->orWhere('b.content', 'like', "%{$search}%")
                        ->orWhere('b.author', 'like', "%{$search}%")
                        ->orWhere('c.name', 'like', "%{$search}%")
                        ->orWhere('b.publish_date', 'like', "%{$search}%")
                        ->orWhere('b.read_time', 'like', "%{$search}%")
                        ->orWhereRaw('JSON_CONTAINS(b.tags, ?)', [json_encode($search)]);
                });
            }


            if ($request->has('category_id') && !empty($request->category_id)) {
                $query->where('b.category_id', $request->category_id);
            }

            $blogs = $query->orderByDesc('b.featured')
                ->get()
                ->map(function ($blog) {
                    $blog->tags = $blog->tags ? json_decode($blog->tags, true) : [];
                    return $blog;
                });

            if ($blogs->isEmpty()) {
                return $this->sendError('No blog found.', $this->backend_error_status);
            }

            return $this->sendResponse(
                $blogs,
                'Blogs retrieved successfully',
                $this->success_status
            );
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);

            return $this->sendError(
                $this->common_error_message,
                $this->exception_status
            );
        }
    }

    public function blogView(Request $request): JsonResponse
    {
        $function_name = 'blogView';

        try {
            $validator = Validator::make($request->all(), [
                'slug' => 'required|exists:blogs,slug',
            ]);

            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            $query = DB::table('blogs as b')
                ->join('blog_categories as c', 'b.category_id', '=', 'c.id')
                ->select(
                    'b.id',
                    'b.category_id',
                    'c.name as category_name',
                    'b.title',
                    'b.slug',
                    'b.excerpt',
                    'b.content',
                    'b.read_time',
                    'b.author',
                    'b.publish_date',
                    'b.tags',
                    DB::raw('CONCAT("' . asset('uploads/blogs') . '/", b.icon) AS icon'),
                    'b.featured',
                )
                ->where('b.slug', $request->slug)->where('b.status', 1);

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('b.title', 'like', "%{$search}%")
                        ->orWhere('b.excerpt', 'like', "%{$search}%")
                        ->orWhere('b.content', 'like', "%{$search}%")
                        ->orWhere('b.author', 'like', "%{$search}%")
                        ->orWhere('c.name', 'like', "%{$search}%")
                        ->orWhere('b.publish_date', 'like', "%{$search}%")
                        ->orWhere('b.read_time', 'like', "%{$search}%")
                        ->orWhereRaw('JSON_CONTAINS(b.tags, ?)', [json_encode($search)]);
                });
            }


            if ($request->has('category_id') && !empty($request->category_id)) {
                $query->where('b.category_id', $request->category_id);
            }

            $blogs = $query->orderByDesc('b.featured')
                ->get()
                ->map(function ($blog) {
                    $blog->tags = $blog->tags ? json_decode($blog->tags, true) : [];
                    return $blog;
                });

            if ($blogs->isEmpty()) {
                return $this->sendError('No blog found.', $this->backend_error_status);
            }

            return $this->sendResponse(
                $blogs,
                'Blog View retrieved successfully',
                $this->success_status
            );
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);

            return $this->sendError(
                $this->common_error_message,
                $this->exception_status
            );
        }
    }
}
