<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;

class AppSettingController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/AppSettingController";
    }

    public function index()
    {
        $function_name = 'index';
        try {
            return view('admin.app-setting.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        $function_name = 'create';
        try {
            return view('admin.app-setting.create');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        $function_name = 'edit';
        try {
            $setting = AppSetting::where('id', decryptId($id))->first();
            if ($setting) {
                return view('admin.app-setting.edit', [
                    'setting' => $setting
                ]);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataSetting(Request $request)
    {
        $function_name = 'getDataSetting';
        try {
            if ($request->ajax()) {
                $setting = DB::table('app_settings')->select('app_settings.*');
                return DataTables::of($setting)
                    ->addColumn('status', function ($setting) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => $setting->status
                        ];
                        return view('admin.render-view.datable-label', [
                            'status_array' => $status_array
                        ])->render();
                    })
                    ->addColumn('action', function ($setting) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'edit_route' => route('admin.app-setting.edit', encryptId($setting->id)),
                            'delete_id' => $setting->id,
                            'current_status' => $setting->status,
                            'hidden_id' => $setting->id,
                        ];
                        return view('admin.render-view.datable-action', [
                            'action_array' => $action_array
                        ])->render();
                    })
                    ->addColumn('image', function ($setting) {
                        if (!empty($setting->image)) {
                            return '<img src="' . asset('uploads/app-settings/' . $setting->image) . '" alt="image" style="width: 50px;">';
                        }
                        return 'N/A';
                    })
                    ->rawColumns(['action', 'status', 'image'])
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
                'key' => [
                    'required',
                    $id == 0 ? 'unique:app_settings,key' : 'unique:app_settings,key,' . $id . ',id',
                ],
                'value' => 'required',
            ];

            $validateMessage = [
                'key.required' => 'The setting key is required.',
                'key.unique' => 'The setting key has already been taken.',
                'value.required' => 'The value is required.',
            ];

            $validator = Validator::make($request_all, $validateArray, $validateMessage);
            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
            }

            if ($id == 0) {
                $image = '';
                if ($request->hasFile('image')) {
                    $image = \App\Helpers\ImageUploadHelper::appSettingImageUpload($request->file('image'));
                }

                AppSetting::create([
                    'screen_name' => $request->screen_name,
                    'key' => $request->key,
                    'value' => $request->value,
                    'image' => $image,
                    'status' => (int) $request->status,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'App setting added successfully'
                ]);
            } else {
                $setting = AppSetting::find($id);
                $image = $setting->image;

                if ($request->hasFile('image')) {
                    if (!empty($setting->image)) {
                        $old_image_path = public_path('uploads/app-settings/' . $setting->image);
                        if (file_exists($old_image_path)) {
                            unlink($old_image_path);
                        }
                    }
                    $image = \App\Helpers\ImageUploadHelper::appSettingImageUpload($request->file('image'));
                }

                AppSetting::where('id', $id)->update([
                    'screen_name' => $request->screen_name,
                    'key' => $request->key,
                    'value' => $request->value,
                    'image' => $image,
                    'status' => (int) $request->status,
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'App setting updated successfully'
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
            AppSetting::where('id', $id)->update(['status' => $status]);
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
            $setting = AppSetting::where('id', $id)->first();
            if ($setting) {
                if (!empty($setting->image)) {
                    $old_image_path = public_path('uploads/app-settings/' . $setting->image);
                    if (file_exists($old_image_path)) {
                        unlink($old_image_path);
                    }
                }
                $setting->delete();
                return response()->json([
                    'message' => 'App setting deleted successfully'
                ]);
            } else {
                return response()->json(['error' => 'Setting not found.'], 500);
            }
        } catch (\Exception $e) {
            logger()->error("$function_name: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }
}
