<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceEssential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use App\Helpers\ImageUploadHelper;
use Illuminate\Support\Facades\File;

class ServiceEssentialController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message', 'Something went wrong!');
        $this->exception_error_code = config('custom.exception_error_code', 500);
        $this->validator_error_code = config('custom.validator_error_code', 422);
        $this->controller_name = "Admin/ServiceEssentialController";
    }

    public function index()
    {
        try {
            return view('admin.service-essentials.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'index');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        try {
            return view('admin.service-essentials.create');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'create');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        try {
            $essential = ServiceEssential::findOrFail(decryptId($id));
            return view('admin.service-essentials.edit', compact('essential'));
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'edit');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataEssential(Request $request)
    {
        try {
            if ($request->ajax()) {
                $essentials = ServiceEssential::query();
                
                if ($request->status !== null && $request->status !== '') {
                    $essentials->where('status', $request->status);
                }

                return DataTables::of($essentials)
                    ->addColumn('status', function ($e) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status'   => $e->status
                        ];
                        return view('admin.render-view.datable-label', compact('status_array'))->render();
                    })
                    ->addColumn('action', function ($e) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'edit_route' => route('admin.service-essential.edit', encryptId($e->id)),
                            'delete_id' => $e->id,
                            'current_status' => $e->status,
                            'hidden_id' => $e->id,
                        ];
                        return view('admin.render-view.datable-action', compact('action_array'))->render();
                    })
                    ->addColumn('icon', function ($e) {
                        if ($e->icon && file_exists(public_path('uploads/essential/' . $e->icon))) {
                            return '<img src="' . asset('uploads/essential/' . $e->icon) . '" style="max-width:50px;" />';
                        }
                        return '-';
                    })
                    ->rawColumns(['action', 'icon', 'status'])
                    ->make(true);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'getDataEssential');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function store(Request $request)
    {
        $id = $request->input('edit_value', 0);

        $rules = [
            'title'  => 'required|string|max:255',
            'type'   => 'nullable|string|max:255',
            'icon'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
        }

        try {
            $icon = null;

            if ($request->hasFile('icon')) {
                $old = ServiceEssential::where('id', $id)->first();
                if ($old && $old->icon) {
                    $filePath = public_path('uploads/essential/' . $old->icon);
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                }
                $icon = ImageUploadHelper::essentialImageUpload($request->file('icon'));
            } elseif ($id != 0) {
                $icon = ServiceEssential::find($id)?->icon;
            }

            $data = [
                'title'   => $request->title,
                'type'    => $request->type,
                'icon'    => $icon,
                'status'  => (int) $request->status,
            ];

            if ($id == 0) {
                ServiceEssential::create($data);
                return response()->json(['success' => true, 'message' => "Essential added successfully"]);
            } else {
                ServiceEssential::where('id', $id)->update($data);
                return response()->json(['success' => true, 'message' => "Essential updated successfully"]);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'store');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function changeStatus($id, $status)
    {
        try {
            ServiceEssential::where('id', $id)->update(['status' => $status]);
            return response()->json(['message' => 'Status changed successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $essential = ServiceEssential::find($id);
            if ($essential) {
                if ($essential->icon) {
                    $filePath = public_path('uploads/essential/' . $essential->icon);
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                }
                $essential->delete();
            }
            return response()->json(['message' => 'Essential deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], 500);
        }
    }
}
