<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;

class AdminStaffController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/AdminStaffController";
    }

    public function index()
    {
        try {
            return view('admin.admin-staff.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'index');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        try {
            $roles = Role::all();
            return view('admin.admin-staff.create', compact('roles'));
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'create');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        try {
            $staff = Admin::where('id', decryptId($id))->first();
            if ($staff) {
                $roles = Role::all();
                return view('admin.admin-staff.edit', compact('staff', 'roles'));
            }
            return redirect()->route('admin.admin-staff.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'edit');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataAdminStaff(Request $request)
    {
        try {
            if ($request->ajax()) {
                $staff = DB::table('admins')
                    ->leftJoin('roles', 'admins.role_id', '=', 'roles.id')
                    ->select('admins.*', 'roles.name as role_name');

                if ($request->status !== null && $request->status !== '') {
                    $staff->where('admins.status', $request->status);
                }

                return DataTables::of($staff)
                    ->addColumn('status', function ($staff) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => $staff->status
                        ];
                        return view('admin.render-view.datable-label', compact('status_array'))->render();
                    })
                    ->addColumn('role_name', function ($staff) {
                        return $staff->role_name ? '<span class="badge badge-light-primary">'.$staff->role_name.'</span>' : '<span class="badge badge-light-secondary">Super Admin</span>';
                    })
                    ->addColumn('action', function ($staff) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'edit_route' => route('admin.admin-staff.edit', encryptId($staff->id)),
                            'delete_id' => $staff->id,
                            'current_status' => $staff->status,
                            'hidden_id' => $staff->id,
                        ];
                        return view('admin.render-view.datable-action', compact('action_array'))->render();
                    })
                    ->rawColumns(['status', 'role_name', 'action'])
                    ->make(true);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'getDataAdminStaff');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function store(Request $request)
    {
        $id = $request->input('edit_value', 0);
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admins,email,' . $id,
            'role_id' => 'nullable|exists:roles,id',
            'status' => 'required|in:0,1',
        ];

        if ($id == 0) {
            $rules['password'] = 'required|string|min:6';
        } else {
            $rules['password'] = 'nullable|string|min:6';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
        }

        try {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'role_id' => $request->role_id ?: null,
                'status' => (int) $request->status,
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            if ($id == 0) {
                Admin::create($data);
                $msg = "Staff added successfully";
            } else {
                Admin::where('id', $id)->update($data);
                $msg = "Staff updated successfully";
            }

            return response()->json(['success' => true, 'message' => $msg]);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'store');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function changeStatus($id, $status)
    {
        try {
            Admin::where('id', $id)->update(['status' => $status]);
            return response()->json(['message' => 'Status changed successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $staff = Admin::find($id);
            if ($staff) {
                // Prevent deleting self
                if ($staff->id == auth('admin')->id()) {
                    return response()->json(['error' => 'You cannot delete yourself!'], 400);
                }
                $staff->delete();
                return response()->json(['message' => 'Staff deleted successfully']);
            }
            return response()->json(['error' => 'Staff not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], 500);
        }
    }
}
