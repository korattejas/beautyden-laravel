<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Filter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;



class FilterController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/FilterController";
    }
    public function index()
    {
        $function_name = 'index';
        try {
            return view('admin.filter.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        $function_name = 'create';
        try {
            $filterShortValues = config('custom.filter_short_value');
            if ($filterShortValues) {
                return view('admin.filter.create', [
                    'filterShortValues' => $filterShortValues
                ]);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        $function_name = 'edit';
        try {
            $filterShortValues = config('custom.filter_short_value');
            $filter = Filter::where('id', decryptId($id))->first();
            if ($filter) {
                return view('admin.filter.edit', [
                    'filterShortValues' => $filterShortValues,
                    'filter' => $filter
                ]);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataFilter(Request $request)
    {
        $function_name = 'getDataFilter';
        try {
            if ($request->ajax()) {
                $filter = DB::table('filters')->select('filters.*');
                return DataTables::of($filter)
                    ->addColumn('status', function ($filter) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => $filter->status
                        ];
                        return view('admin.render-view.datable-label', [
                            'status_array' => $status_array
                        ])->render();
                    })
                    ->addColumn('is_main', function ($filter) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => 3,
                            'current_is_main_priority_status' => $filter->is_main
                        ];
                        return view('admin.render-view.datable-label', [
                            'status_array' => $status_array
                        ])->render();
                    })
                    ->addColumn('action', function ($filter) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'edit_route' => route('admin.filter.edit', encryptId($filter->id)),
                            'delete_id' => $filter->id,
                            'current_status' => $filter->status,
                            'current_is_main_priority_status' => $filter->is_main,
                            'hidden_id' => $filter->id,
                        ];
                        return view('admin.render-view.datable-action', [
                            'action_array' => $action_array
                        ])->render();
                    })
                    ->rawColumns(['action', 'status', 'is_main'])
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
                    $id == 0 ? 'unique:filters,name' : 'unique:filters,name,' . $id . ',id',
                ],
                'values' => 'required|array',
            ];

            $validateMessage = [
                'name.required' => 'The filter name is required.',
                'name.unique' => 'The filter name has already been taken.',
                'values.required' => 'The values is required.',
            ];


            $validator = Validator::make($request_all, $validateArray, $validateMessage);
            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
            }

            if ($id == 0) {
                $selectedValues = implode(',', $request->values);
                Filter::create([
                    'name' => $request->name,
                    'values' => $selectedValues,
                    'is_main' => (int) $request->is_main,
                    'status' => (int) $request->status,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => trans('admin_string.filter_added_successfully')
                ]);
            } else {
                $selectedValues = implode(',', $request->values);
                Filter::where('id', $id)->update([
                    'name' => $request->name,
                    'values' => $selectedValues,
                    'is_main' => (int) $request->is_main,
                    'status' => (int) $request->status,
                ]);
                return response()->json([
                    'success' => true,
                    'message' => trans('admin_string.filter_updated_successfully')
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
            Filter::where('id', $id)->update(['status' => $status]);
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
            Filter::where('id', $id)->update(['is_main' => $status]);
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
            $filter = Filter::where('id', $id)->first();
            if ($filter) {
                $filter->delete();

                return response()->json([
                    'message' => trans('admin_string.filter_deleted_successfully')
                ]);
            } else {
                logger()->error("$function_name: Filter found.");
                return response()->json(['error' => ' Filter found.'], 500);
            }

        } catch (\Exception $e) {
            logger()->error("$function_name: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }


}
