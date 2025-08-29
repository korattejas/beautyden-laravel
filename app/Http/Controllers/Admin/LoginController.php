<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\AdminLoginRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class LoginController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/LoginController";
    }
    public function index()
    {
        $function_name = 'index';
        try {
            if (Auth::guard('admin')->check() && Auth::guard('admin')->user()) {
                return redirect()->route('admin.dashboard');
            }
            return view('admin.auth.login');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }


    public function loginCheck(Request $request): \Illuminate\Http\JsonResponse
    {
        $function_name = 'loginCheck';
        try {
            if (auth()->guard('admin')->attempt(['email' => $request->login_email, 'password' => $request->login_password, 'status' => "1"])) {
                if (Auth::guard('admin')->user()->status == '1') {
                    return response()->json([
                        'message' => trans('admin_string.login_success'),
                    ]);
                } else {
                    return response()->json([
                        'message' => trans('admin_string.account_deactivate'),
                    ], 404);
                }
            } else {
                return response()->json([
                    'message' => trans('admin_string.invalid_email_password'),
                ], 404);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function logout(Request $request): \Illuminate\Http\RedirectResponse
    {
        $function_name = 'logout';
        try {
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

}
