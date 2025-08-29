<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeCounter;
use Illuminate\Http\Request;
use App\Helpers\ImageUploadHelper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;


class HomeCounterController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/HomeCounterController";
    }

    public function index()
    {
        $function_name = 'index';
        try {
            return view('admin.home-counters.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        $function_name = 'create';
        try {
            return view('admin.home-counters.create');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        $function_name = 'edit';
        try {
            $counter = HomeCounter::where('id', decryptId($id))->first();
            if ($counter) {
                return view('admin.home-counters.edit', [
                    'homeCounter' => $counter
                ]);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataHomeCounters(Request $request)
    {
        $function_name = 'getDataHomeCounters';
        try {
            if ($request->ajax()) {
                $counters = DB::table('home_counters')->select('home_counters.*');
                return DataTables::of($counters)
                    ->addColumn('status', function ($counter) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => $counter->status
                        ];
                        return view('admin.render-view.datable-label', [
                            'status_array' => $status_array
                        ])->render();
                    })
                    ->addColumn('action', function ($counter) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'edit_route' => route('admin.home-counters.edit', encryptId($counter->id)),
                            'delete_id' => $counter->id,
                            'current_status' => $counter->status,
                            'hidden_id' => $counter->id,
                        ];
                        return view('admin.render-view.datable-action', [
                            'action_array' => $action_array
                        ])->render();
                    })
                    ->addColumn('icon', function ($categories) {
                        if ($categories->icon && file_exists(public_path('uploads/home-counters/' . $categories->icon))) {
                            $imageUrl = asset('uploads/home-counters/' . $categories->icon);
                            return '<img src="' . $imageUrl . '" style="max-width:100px;" alt="Category Icon" />';
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

    public function store(Request $request)
    {
        $function_name = 'store';
        $request_all = request()->all();
        try {
            $id = $request->input('edit_value');
            $validateArray = [
                'label' => [
                    'required',
                    $id == 0 ? 'unique:home_counters,label' : 'unique:home_counters,label,' . $id . ',id',
                ],
                'value' => 'required',
                'icon' => $id == 0 ? 'image|mimes:jpeg,png,jpg,gif,svg' : 'image|mimes:jpeg,png,jpg,gif,svg',
            ];

            $validateMessage = [
                'label.required' => 'The label is required.',
                'label.unique' => 'This label has already been taken.',
                'value.required' => 'The value is required.',
                'icon.image' => 'The file must be an image.',
                'icon.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, svg.',
            ];

            $validator = Validator::make($request_all, $validateArray, $validateMessage);
            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
            }

            if ($id == 0) {
                if ($request->hasFile('icon')) {
                    $icon = ImageUploadHelper::homeCounterImageUpload($request->icon);
                }
                HomeCounter::create([
                    'label' => $request->label,
                    'value' => $request->value,
                    'icon' => $icon ?? null,
                    'status' => (int) $request->status,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "Home counter added successfully"
                ]);
            } else {
                $homeCounter = HomeCounter::where('id', $id)->first();

                if ($request->hasFile('icon')) {
                    $icon = ImageUploadHelper::homeCounterImageUpload($request->icon);
                } else {
                    $icon = $homeCounter->icon;
                }

                HomeCounter::where('id', $id)->update([
                    'label' => $request->label,
                    'value' => $request->value,
                    'icon' => $icon ?? null,
                    'status' => (int) $request->status,
                ]);
                return response()->json([
                    'success' => true,
                    'message' => "Home counter updated successfully"
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
            HomeCounter::where('id', $id)->update(['status' => $status]);
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
            $counter = HomeCounter::where('id', $id)->first();
            if ($counter) {
                $filePath = public_path('uploads/home-counters/' . $counter->icon);

                if (File::exists($filePath)) {
                    File::delete($filePath);
                }

                $counter->delete();
                return response()->json([
                    'message' => 'Home counter deleted successfully'
                ]);
            } else {
                logger()->error("$function_name: No counter found.");
                return response()->json(['error' => 'No counter found.'], 500);
            }
        } catch (\Exception $e) {
            logger()->error("$function_name: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }
}
