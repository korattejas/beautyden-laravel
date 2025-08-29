<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/UserController";
    }
    public function index()
    {
        $function_name = 'index';
        try {
            return view('admin.user.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataUser(Request $request)
    {
        $function_name = 'getDataUser';
        try {
            if ($request->ajax()) {
                $users = DB::table('users')->select('users.*');
                return DataTables::of($users)
                    ->addColumn('status', function ($users) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => $users->status
                        ];
                        return view('admin.render-view.datable-label', [
                            'status_array' => $status_array
                        ])->render();
                    })
                    ->addColumn('action', function ($users) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'delete_id' => $users->id,
                            'current_status' => $users->status,
                            'hidden_id' => $users->id,
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

    public function changeStatus($id, $status)
    {
        $function_name = 'changeStatus';
        try {
            User::where('id', $id)->update(['status' => $status]);
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
            $user = User::where('id', $id)->first();
            if ($user) {
                $user->delete();

                return response()->json([
                    'message' => trans('admin_string.user_deleted_successfully')
                ]);
            } else {
                logger()->error("$function_name: Failed to delete the user not found.");
                return response()->json(['error' => 'Failed to delete the user not found..'], 500);
            }

        } catch (\Exception $e) {
            logger()->error("$function_name: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }


}
