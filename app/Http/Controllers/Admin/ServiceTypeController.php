<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceType;
use App\Helpers\ImageUploadHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;

class ServiceTypeController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/ServiceTypeController";
    }

    public function index()
    {
        $function_name = 'index';
        try {
            return view('admin.service-type.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        $function_name = 'create';
        try {
            return view('admin.service-type.create');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        $function_name = 'edit';
        try {
            $type = ServiceType::where('id', decryptId($id))->first();
            if ($type) {
                return view('admin.service-type.edit', [
                    'type' => $type
                ]);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataServiceType(Request $request)
    {
        $function_name = 'getDataServiceType';
        try {
            if ($request->ajax()) {
                $types = DB::table('service_types')->select('service_types.*');

                if ($request->status !== null && $request->status !== '') {
                    $types->where('service_types.status', $request->status);
                }

                if ($request->popular !== null && $request->popular !== '') {
                    $types->where('service_types.is_popular', $request->popular);
                }

                if ($request->is_new !== null && $request->is_new !== '') {
                    $types->where('service_types.is_new', $request->is_new);
                }

                if ($request->created_date) {
                    $types->whereDate('service_types.created_at', $request->created_date);
                }

                return DataTables::of($types)
                    ->addColumn('status', function ($types) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => $types->status
                        ];
                        return view('admin.render-view.datable-label', [
                            'status_array' => $status_array
                        ])->render();
                    })
                    ->addColumn('is_popular', function ($types) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => 3,
                            'current_is_popular_priority_status' => $types->is_popular
                        ];
                        return view('admin.render-view.datable-label', [
                            'status_array' => $status_array
                        ])->render();
                    })
                    ->addColumn('is_new', function ($types) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => 4,
                            'current_is_new_priority_status' => $types->is_new
                        ];
                        return view('admin.render-view.datable-label', [
                            'status_array' => $status_array
                        ])->render();
                    })
                    ->addColumn('action', function ($types) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'edit_route' => route('admin.service-type.edit', encryptId($types->id)),
                            'delete_id' => $types->id,
                            'current_status' => $types->status,
                            'current_is_popular_priority_status' => $types->is_popular,
                            'current_is_new_status' => $types->is_new,
                            'hidden_id' => $types->id,
                        ];
                        return view('admin.render-view.datable-action', [
                            'action_array' => $action_array
                        ])->render();
                    })
                    ->addColumn('icon', function ($types) {
                        if ($types->icon && file_exists(public_path('uploads/service-types/' . $types->icon))) {
                            $imageUrl = asset('uploads/service-types/' . $types->icon);
                            return '<img src="' . $imageUrl . '" style="max-width:100px;" alt="Icon" />';
                        }
                        return '';
                    })
                    ->rawColumns(['action', 'icon', 'status', 'is_popular', 'is_new'])
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
                'name' => [
                    'required',
                    $id == 0 ? 'unique:service_types,name' : 'unique:service_types,name,' . $id . ',id',
                ],
                'icon' => 'image|mimes:jpeg,png,jpg,gif,svg,webp',
            ];

            $validateMessage = [
                'name.required' => 'The name is required.',
                'name.unique' => 'The name has already been taken.',
                'icon.image' => 'The file must be an image.',
                'icon.mimes' => 'The image must be a valid format.',
            ];

            $validator = Validator::make($request_all, $validateArray, $validateMessage);
            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
            }

            if ($id == 0) {
                if ($request->hasFile('icon')) {
                    // Reusing the same helper, we'll create a new method for service types if needed or just use standard upload
                    $file = $request->file('icon');
                    $filename = time() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/service-types'), $filename);
                    $icon = $filename;
                }

                ServiceType::create([
                    'name' => $request->name,
                    'description' => $request->description,
                    'icon' => $icon ?? null,
                    'is_popular' => (int) $request->is_popular,
                    'is_new' => (int) $request->is_new,
                    'status' => (int) $request->status,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "Service type added successfully"
                ]);
            } else {
                $type = ServiceType::where('id', $id)->first();

                if ($request->hasFile('icon')) {
                    $filePath = public_path('uploads/service-types/' . $type->icon);
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                    $file = $request->file('icon');
                    $filename = time() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/service-types'), $filename);
                    $icon = $filename;
                } else {
                    $icon = $type->icon;
                }

                ServiceType::where('id', $id)->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'icon' => $icon,
                    'is_popular' => (int) $request->is_popular,
                    'is_new' => (int) $request->is_new,
                    'status' => (int) $request->status,
                ]);
                return response()->json([
                    'success' => true,
                    'message' => "Service type edited successfully"
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
            ServiceType::where('id', $id)->update(['status' => $status]);
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
            ServiceType::where('id', $id)->update(['is_popular' => $status]);
            return response()->json(['message' => trans('admin_string.msg_priority_status_change')]);
        } catch (\Exception $e) {
            logger()->error("$function_name: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function changeIsNewStatus($id, $status)
    {
        $function_name = 'changeIsNewStatus';
        try {
            ServiceType::where('id', $id)->update(['is_new' => $status]);
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
            $type = ServiceType::where('id', $id)->first();
            if ($type) {
                $filePath = public_path('uploads/service-types/' . $type->icon);
                if (File::exists($filePath)) {
                    File::delete($filePath);
                }
                $type->delete();
                return response()->json(['message' => 'Service type deleted successfully.']);
            } else {
                return response()->json(['error' => 'No type found.'], 500);
            }
        } catch (\Exception $e) {
            logger()->error("$function_name: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }
}
