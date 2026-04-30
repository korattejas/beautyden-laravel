<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceSubcategory;
use App\Models\ServiceCategory;
use App\Helpers\ImageUploadHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;

class ServiceSubcategoryController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/ServiceSubcategoryController";
    }

    public function index()
    {
        $function_name = 'index';
        try {
            return view('admin.service-subcategory.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        $function_name = 'create';
        try {
            $categories = ServiceCategory::where('status', 1)->get();
            return view('admin.service-subcategory.create', compact('categories'));
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        $function_name = 'edit';
        try {
            $subcategory = ServiceSubcategory::where('id', decryptId($id))->first();
            $categories = ServiceCategory::where('status', 1)->get();
            if ($subcategory) {
                return view('admin.service-subcategory.edit', [
                    'subcategory' => $subcategory,
                    'categories' => $categories
                ]);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataServiceSubcategory(Request $request)
    {
        $function_name = 'getDataServiceSubcategory';
        try {
            if ($request->ajax()) {
                $subcategories = DB::table('service_subcategories')
                    ->join('service_categories', 'service_categories.id', '=', 'service_subcategories.service_category_id')
                    ->select('service_subcategories.*', 'service_categories.name as category_name');

                if ($request->status !== null && $request->status !== '') {
                    $subcategories->where('service_subcategories.status', $request->status);
                }

                if ($request->popular !== null && $request->popular !== '') {
                    $subcategories->where('service_subcategories.is_popular', $request->popular);
                }

                if ($request->created_date) {
                    $subcategories->whereDate('service_subcategories.created_at', $request->created_date);
                }

                return DataTables::of($subcategories)
                    ->addColumn('status', function ($sub) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => $sub->status
                        ];
                        return view('admin.render-view.datable-label', [
                            'status_array' => $status_array
                        ])->render();
                    })
                    ->addColumn('is_popular', function ($sub) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => 3,
                            'current_is_popular_priority_status' => $sub->is_popular
                        ];
                        return view('admin.render-view.datable-label', [
                            'status_array' => $status_array
                        ])->render();
                    })
                    ->addColumn('action', function ($sub) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'edit_route' => route('admin.service-subcategory.edit', encryptId($sub->id)),
                            'delete_id' => $sub->id,
                            'current_status' => $sub->status,
                            'current_is_popular_priority_status' => $sub->is_popular,
                            'hidden_id' => $sub->id,
                        ];
                        return view('admin.render-view.datable-action', [
                            'action_array' => $action_array
                        ])->render();
                    })
                    ->addColumn('icon', function ($sub) {
                        if ($sub->icon && file_exists(public_path('uploads/service-subcategory/' . $sub->icon))) {
                            $imageUrl = asset('uploads/service-subcategory/' . $sub->icon);
                            return '<img src="' . $imageUrl . '" style="max-width:100px;" alt="Subcategory Icon" />';
                        }
                        return '';
                    })
                    ->addColumn('starting_at_price', function ($sub) {
                        return '₹' . number_format($sub->starting_at_price, 2);
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
                'service_category_id' => 'required|exists:service_categories,id',
                'name' => 'required',
                'starting_at_price' => 'required|numeric|min:0',
                'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
                'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
                'gallery_videos.*' => 'nullable|mimes:mp4,mov,ogg,qt,webm|max:50000',
            ];

            $validateMessage = [
                'service_category_id.required' => 'The parent category is required.',
                'service_category_id.exists' => 'The selected category is invalid.',
                'name.required' => 'The subcategory name is required.',
                'icon.image' => 'The file must be an image.',
                'icon.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, svg, webp.',
                'gallery_images.*.image' => 'Each gallery file must be an image.',
                'gallery_videos.*.mimes' => 'Each video must be a valid video format.',
                'gallery_videos.*.uploaded' => 'The video failed to upload. This usually happens if the file is too large for the server.',
                'gallery_videos.*.max' => 'The video size cannot exceed 50MB.',
            ];

            $validator = Validator::make($request_all, $validateArray, $validateMessage);
            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
            }

            $media_json = [
                'images' => [],
                'videos' => []
            ];

            if ($id != 0) {
                $subcategory = ServiceSubcategory::where('id', $id)->first();
                $media_json = $subcategory->media_json ?? $media_json;

                // Handle Removed Media
                if ($request->has('removed_media')) {
                    foreach ($request->removed_media as $removed) {
                        // Check in images
                        if (($key = array_search($removed, $media_json['images'])) !== false) {
                            unset($media_json['images'][$key]);
                            $filePath = public_path('uploads/service-media/' . $removed);
                            if (File::exists($filePath)) File::delete($filePath);
                        }
                        // Check in videos
                        if (($key = array_search($removed, $media_json['videos'])) !== false) {
                            unset($media_json['videos'][$key]);
                            $filePath = public_path('uploads/service-media/' . $removed);
                            if (File::exists($filePath)) File::delete($filePath);
                        }
                    }
                    $media_json['images'] = array_values($media_json['images']);
                    $media_json['videos'] = array_values($media_json['videos']);
                }
            }

            // Upload New Images
            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $image) {
                    $media_json['images'][] = ImageUploadHelper::serviceMediaUpload($image);
                }
            }

            // Upload New Videos
            if ($request->hasFile('gallery_videos')) {
                foreach ($request->file('gallery_videos') as $video) {
                    $media_json['videos'][] = ImageUploadHelper::serviceMediaUpload($video);
                }
            }

            if ($id == 0) {
                if ($request->hasFile('icon')) {
                    $icon = ImageUploadHelper::serviceSubcategoryImageUpload($request->icon);
                }

                ServiceSubcategory::create([
                    'service_category_id' => $request->service_category_id,
                    'name' => $request->name,
                    'starting_at_price' => $request->starting_at_price,
                    'description' => $request->description,
                    'icon' => $icon ?? null,
                    'media_json' => $media_json,
                    'is_popular' => (int) $request->is_popular,
                    'status' => (int) $request->status,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "Service subcategory added successfully"
                ]);
            } else {
                if ($request->hasFile('icon')) {
                    $filePath = public_path('uploads/service-subcategory/' . $subcategory->icon);

                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                    $icon = ImageUploadHelper::serviceSubcategoryImageUpload($request->icon);
                } else {
                    $icon = $subcategory->icon;
                }

                $subcategory->update([
                    'service_category_id' => $request->service_category_id,
                    'name' => $request->name,
                    'starting_at_price' => $request->starting_at_price,
                    'description' => $request->description,
                    'icon' => $icon,
                    'media_json' => $media_json,
                    'is_popular' => (int) $request->is_popular,
                    'status' => (int) $request->status,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "Service subcategory edited successfully"
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
            ServiceSubcategory::where('id', $id)->update(['status' => $status]);
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
            ServiceSubcategory::where('id', $id)->update(['is_popular' => $status]);
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
            $subcategory = ServiceSubcategory::where('id', $id)->first();
            if ($subcategory) {
                $filePath = public_path('uploads/service-subcategory/' . $subcategory->icon);

                if (File::exists($filePath)) {
                    File::delete($filePath);
                }

                // Delete gallery media
                if ($subcategory->media_json) {
                    $media = $subcategory->media_json;
                    $all_media = array_merge($media['images'] ?? [], $media['videos'] ?? []);
                    foreach ($all_media as $item) {
                        $mPath = public_path('uploads/service-media/' . $item);
                        if (File::exists($mPath)) File::delete($mPath);
                    }
                }

                $subcategory->delete();

                return response()->json([
                    'message' => trans('admin_string.subcategory_deleted_successfully')
                ]);
            } else {
                logger()->error("$function_name: No subcategory found.");
                return response()->json(['error' => 'No subcategory found.'], 500);
            }
        } catch (\Exception $e) {
            logger()->error("$function_name: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }
}
