<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Policy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class PoliciesController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/PoliciesController";
    }

    public function createOrUpdate()
    {
        $function_name = 'createOrUpdate';
        try {
            $policies = Policy::pluck('description', 'type')->toArray();
            return view('admin.policies.create_update', compact('policies'));
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function store(Request $request)
    {
        $function_name = 'store';
        $request_all = $request->all();

        try {
            $validateArray = [
                'payment_policy'   => 'required|string',
                'privacy_policy'   => 'required|string',
                'terms_conditions' => 'required|string',
            ];

            $validator = Validator::make($request_all, $validateArray);
            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
            }

            $data = [
                'payment_policy'   => $request->payment_policy,
                'privacy_policy'   => $request->privacy_policy,
                'terms_conditions' => $request->terms_conditions,
            ];

            foreach ($data as $type => $desc) {
                Policy::updateOrCreate(
                    ['type' => $type],
                    ['description' => $desc]
                );
            }

            return response()->json([
                'success' => true,
                'message' => "Policies saved successfully"
            ]);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }
}
