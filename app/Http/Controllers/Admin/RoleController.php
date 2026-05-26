<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class RoleController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/RoleController";
    }

    public function index()
    {
        try {
            return view('admin.roles.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'index');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        try {
            return view('admin.roles.create');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'create');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        try {
            $role = Role::where('id', decryptId($id))->first();
            if ($role) {
                return view('admin.roles.edit', compact('role'));
            }
            return redirect()->route('admin.roles.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'edit');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataRoles(Request $request)
    {
        try {
            if ($request->ajax()) {
                $roles = DB::table('roles')->select('roles.*');

                return DataTables::of($roles)
                    ->addColumn('permissions', function ($role) {
                        $perms = json_decode($role->permissions, true);
                        if (is_string($perms)) {
                            $perms = json_decode($perms, true);
                        }
                        if (!is_array($perms)) {
                            $perms = [];
                        }
                        $badges = '';
                        foreach ($perms as $p) {
                            $badges .= '<span class="badge badge-light-success me-50">'.ucfirst(str_replace('_', ' ', $p)).'</span>';
                        }
                        return $badges ?: '<span class="text-muted">None</span>';
                    })
                    ->addColumn('action', function ($role) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'edit_route' => route('admin.roles.edit', encryptId($role->id)),
                            'delete_id' => $role->id,
                            'hidden_id' => $role->id,
                        ];
                        // Avoid passing statuses that don't exist
                        // Since there's no status column in roles table, we might need a custom action view or adjust datable-action
                        // If datable-action requires current_status, we pass null or adjust it.
                        // We will pass empty array for status to prevent errors if the blade needs it.
                        $action_array['current_status'] = null;
                        
                        return view('admin.render-view.datable-action', compact('action_array'))->render();
                    })
                    ->rawColumns(['permissions', 'action'])
                    ->make(true);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'getDataRoles');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function store(Request $request)
    {
        $id = $request->input('edit_value', 0);
        $rules = [
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'permissions' => 'nullable|array',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
        }

        try {
            $data = [
                'name' => $request->name,
                'permissions' => $request->permissions ?: [],
            ];

            if ($id == 0) {
                Role::create($data);
                $msg = "Role created successfully";
            } else {
                Role::where('id', $id)->update($data);
                $msg = "Role updated successfully";
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
            $role = Role::find($id);
            if ($role) {
                $role->delete();
                return response()->json(['message' => 'Role deleted successfully']);
            }
            return response()->json(['error' => 'Role not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], 500);
        }
    }
}
