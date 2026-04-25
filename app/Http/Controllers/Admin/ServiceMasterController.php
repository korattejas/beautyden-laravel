<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceMaster;
use App\Models\ServiceCategory;
use App\Models\ServiceSubcategory;
use App\Models\ServiceEssential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use App\Helpers\ImageUploadHelper;
use Illuminate\Support\Facades\File;

class ServiceMasterController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message', 'Something went wrong!');
        $this->exception_error_code = config('custom.exception_error_code', 500);
        $this->validator_error_code = config('custom.validator_error_code', 422);
        $this->controller_name = "Admin/ServiceMasterController";
    }

    public function index()
    {
        try {
            return view('admin.service-masters.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'index');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        try {
            $categories = ServiceCategory::where('status', 1)->get();
            $essentials = ServiceEssential::where('status', 1)->get();
            return view('admin.service-masters.create', compact('categories', 'essentials'));
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'create');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        try {
            $service = ServiceMaster::findOrFail(decryptId($id));
            $categories = ServiceCategory::where('status', 1)->get();
            $subcategories = ServiceSubcategory::where('service_category_id', $service->category_id)->where('status', 1)->get();
            $essentials = ServiceEssential::where('status', 1)->get();
            return view('admin.service-masters.edit', compact('service', 'categories', 'subcategories', 'essentials'));
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'edit');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function show($id)
    {
        try {
            $service = ServiceMaster::findOrFail(decryptId($id));
            $essentials = ServiceEssential::where('status', 1)->get()->keyBy('id');
            return view('admin.service-masters.show', compact('service', 'essentials'));
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'show');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataServiceMaster(Request $request)
    {
        try {
            if ($request->ajax()) {
                $services = ServiceMaster::query()
                    ->leftJoin('service_categories as sc', 'sc.id', '=', 'service_masters.category_id')
                    ->leftJoin('service_subcategories as ssc', 'ssc.id', '=', 'service_masters.sub_category_id')
                    ->select('service_masters.*', 'sc.name as category_name', 'ssc.name as subcategory_name');

                if ($request->status !== null && $request->status !== '') {
                    $services->where('service_masters.status', $request->status);
                }

                return DataTables::of($services)
                    ->editColumn('is_popular', function ($s) {
                        return $s->is_popular 
                            ? '<span class="badge badge-glow bg-success">Popular</span>' 
                            : '<span class="badge badge-glow bg-secondary">No</span>';
                    })
                    ->addColumn('status', function ($s) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status'   => $s->status
                        ];
                        return view('admin.render-view.datable-label', compact('status_array'))->render();
                    })
                    ->addColumn('action', function ($s) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'is_view_action' => 1,
                            'view_route' => route('admin.service-master.show', encryptId($s->id)),
                            'edit_route' => route('admin.service-master.edit', encryptId($s->id)),
                            'delete_id' => $s->id,
                            'current_status' => $s->status,
                            'hidden_id' => $s->id,
                        ];
                        return view('admin.render-view.datable-action', compact('action_array'))->render();
                    })
                    ->addColumn('view_url', function($s) {
                        return route('admin.service-master.show', encryptId($s->id));
                    })
                    ->rawColumns(['action', 'status', 'is_popular'])
                    ->make(true);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'getDataServiceMaster');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function store(Request $request)
    {
        $id = $request->input('edit_value', 0);

        $rules = [
            'name'            => 'required|string|max:255',
            'category_id'     => 'required|exists:service_categories,id',
            'sub_category_id' => 'nullable|exists:service_subcategories,id',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
        }

        try {
            $service = $id ? ServiceMaster::find($id) : null;

            // Handle Single Icon
            $icon = $service ? $service->icon : null;
            if ($request->hasFile('icon')) {
                if ($icon) File::delete(public_path('uploads/service/' . $icon));
                $icon = ImageUploadHelper::serviceimageUpload($request->file('icon'));
            }

            // Handle Banner Media (Reverting to row-based structure)
            $banner_media = [];
            if ($request->banner) {
                foreach ($request->banner as $key => $item) {
                    $file_url = $item['old_file'] ?? null;
                    $type = $item['old_type'] ?? 'image';

                    if ($request->hasFile("banner.$key.file")) {
                        if ($file_url) File::delete(public_path('uploads/service-media/' . $file_url));
                        $file = $request->file("banner.$key.file");
                        $file_url = ImageUploadHelper::serviceMediaUpload($file);
                        $extension = strtolower($file->getClientOriginalExtension());
                        $type = in_array($extension, ['mp4', 'mov', 'avi', 'wmv']) ? 'video' : 'image';
                    }

                    if ($file_url) {
                        $banner_media[] = ['url' => $file_url, 'type' => $type];
                    }
                }
            }

            // Handle Before / After Results
            $before_after = [];
            if ($request->old_ba_images) {
                foreach ($request->old_ba_images as $old_img) {
                    $before_after[] = $old_img;
                }
            }
            if ($request->hasFile('ba_images')) {
                foreach ($request->file('ba_images') as $file) {
                    $before_after[] = ImageUploadHelper::serviceMediaUpload($file);
                }
            }

            // Handle Dynamic Content Builder
            $content_builder = [];
            if ($request->sections) {
                foreach ($request->sections as $key => $section) {
                    $type = $section['type'];
                    $processed_section = ['type' => $type];

                    if ($type == 'overview') {
                        $processed_section['essential_ids'] = $section['essential_ids'] ?? [];
                    } 
                    elseif ($type == 'ritual' || $type == 'procedure') {
                        $processed_section['title'] = $section['title'] ?? '';
                        $steps = [];
                        if (isset($section['steps'])) {
                            foreach ($section['steps'] as $sKey => $step) {
                                $img = $step['old_image'] ?? null;
                                if ($request->hasFile("sections.$key.steps.$sKey.image")) {
                                    if ($img) File::delete(public_path('uploads/service-content/' . $img));
                                    $img = ImageUploadHelper::serviceContentImageUpload($request->file("sections.$key.steps.$sKey.image"));
                                }
                                $steps[] = ['title' => $step['title'] ?? '', 'desc'  => $step['desc'] ?? '', 'image' => $img];
                            }
                        }
                        $processed_section['steps'] = $steps;
                    }
                    elseif ($type == 'expert') {
                        $img = $section['old_image'] ?? null;
                        if ($request->hasFile("sections.$key.image")) {
                            if ($img) File::delete(public_path('uploads/service-content/' . $img));
                            $img = ImageUploadHelper::serviceContentImageUpload($request->file("sections.$key.image"));
                        }
                        $processed_section['image'] = $img;
                        $processed_section['points'] = array_values(array_filter($section['points'] ?? []));
                    }
                    elseif ($type == 'list') {
                        $processed_section['title'] = $section['title'] ?? '';
                        $processed_section['points'] = array_values(array_filter($section['points'] ?? []));
                    }
                    elseif ($type == 'protocol') {
                        $processed_section['title'] = $section['title'] ?? '';
                        $items = [];
                        if (isset($section['items'])) {
                            foreach ($section['items'] as $iKey => $item) {
                                $img = $item['old_image'] ?? null;
                                if ($request->hasFile("sections.$key.items.$iKey.image")) {
                                    if ($img) File::delete(public_path('uploads/service-content/' . $img));
                                    $img = ImageUploadHelper::serviceContentImageUpload($request->file("sections.$key.items.$iKey.image"));
                                }
                                $items[] = ['title' => $item['title'] ?? '', 'image' => $img];
                            }
                        }
                        $processed_section['items'] = $items;
                    }
                    $content_builder[] = $processed_section;
                }
            }

            $data = [
                'category_id'         => $request->category_id,
                'sub_category_id'     => $request->sub_category_id,
                'name'                => $request->name,
                'price'               => $request->price,
                'discount_price'      => $request->discount_price,
                'duration'            => $request->duration,
                'rating'              => $request->rating,
                'reviews'             => $request->reviews,
                'description'         => $request->description,
                'icon'                => $icon,
                'banner_media'        => $banner_media,
                'before_after'        => $before_after,
                'content_json'        => $content_builder,
                'is_popular'          => (int) $request->is_popular,
                'status'              => (int) $request->status,
            ];

            if ($id == 0) {
                ServiceMaster::create($data);
                $msg = "Service catalog created successfully";
            } else {
                ServiceMaster::where('id', $id)->update($data);
                $msg = "Service catalog updated successfully";
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
            $service = ServiceMaster::find($id);
            if ($service) {
                // Cleanup icon
                if($service->icon) File::delete(public_path('uploads/service/' . $service->icon));
                
                // Cleanup medias
                if($service->banner_media) {
                    foreach($service->banner_media as $m) File::delete(public_path('uploads/service-media/' . $m['url']));
                }
                if($service->before_after) {
                    foreach($service->before_after as $ba) {
                        $img = is_array($ba) ? ($ba['before'] ?? ($ba['after'] ?? null)) : $ba;
                        if($img) File::delete(public_path('uploads/service-media/' . $img));
                    }
                }
                
                if ($service->content_json) {
                    foreach ($service->content_json as $section) {
                        if (isset($section['image']) && $section['image']) File::delete(public_path('uploads/service-content/' . $section['image']));
                        if (isset($section['steps'])) {
                            foreach ($section['steps'] as $step) {
                                if (!empty($step['image'])) File::delete(public_path('uploads/service-content/' . $step['image']));
                            }
                        }
                        if (isset($section['items'])) {
                            foreach ($section['items'] as $item) {
                                if (!empty($item['image'])) File::delete(public_path('uploads/service-content/' . $item['image']));
                            }
                        }
                    }
                }
                $service->delete();
            }
            return response()->json(['message' => 'Service deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], 500);
        }
    }

    public function getSubcategories($categoryId)
    {
        return response()->json(ServiceSubcategory::where('service_category_id', $categoryId)->where('status', 1)->get());
    }

    public function changeStatus($id, $status)
    {
        try {
            ServiceMaster::where('id', $id)->update(['status' => $status]);
            return response()->json(['message' => 'Status changed successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], 500);
        }
    }
}
