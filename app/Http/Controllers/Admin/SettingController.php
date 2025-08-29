<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;



class SettingController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/SettingController";
    }
    public function index()
    {
        $function_name = 'index';
        try {
            return view('admin.setting.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        $function_name = 'create';
        try {
            return view('admin.setting.create');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        $function_name = 'edit';
        try {
            $setting = Setting::where('id', decryptId($id))->first();
            if ($setting) {
                return view('admin.setting.edit', [
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
                $setting = DB::table('settings')->select('settings.*');
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
                            'edit_route' => route('admin.setting.edit', encryptId($setting->id)),
                            'delete_id' => $setting->id,
                            'current_status' => $setting->status,
                            'hidden_id' => $setting->id,
                        ];
                        return view('admin.render-view.datable-action', [
                            'action_array' => $action_array
                        ])->render();
                    })
                    ->rawColumns(['action', 'status'])
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
                    $id == 0 ? 'unique:settings,key' : 'unique:settings,key,' . $id . ',id',
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
                Setting::create([
                    'screen_name' => $request->screen_name,
                    'key' => $request->key,
                    'value' => $request->value,
                    'status' => (int) $request->status,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => trans('admin_string.setting_added_successfully')
                ]);
            } else {
                Setting::where('id', $id)->update([
                    'screen_name' => $request->screen_name,
                    'key' => $request->key,
                    'value' => $request->value,
                    'status' => (int) $request->status,
                ]);
                return response()->json([
                    'success' => true,
                    'message' => trans('admin_string.setting_updated_successfully')
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
            Setting::where('id', $id)->update(['status' => $status]);
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
            $setting = Setting::where('id', $id)->first();
            if ($setting) {
                $setting->delete();

                return response()->json([
                    'message' => trans('admin_string.setting_deleted_successfully')
                ]);
            } else {
                logger()->error("$function_name: Setting found.");
                return response()->json(['error' => ' Setting found.'], 500);
            }

        } catch (\Exception $e) {
            logger()->error("$function_name: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }


}
