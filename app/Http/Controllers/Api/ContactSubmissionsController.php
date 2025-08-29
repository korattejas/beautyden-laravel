<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Exception;

class ContactSubmissionsController extends Controller
{
    protected int $success_status;
    protected int $exception_status;
    protected int $backend_error_status;
    protected int $validation_error_status;
    protected string $common_error_message;
    protected string $controller_name;

    public function __construct()
    {
        $this->controller_name = 'API/ContactSubmissionsController';
        $this->success_status = config('custom.status_code_for_success', 200);
        $this->exception_status = config('custom.status_code_for_exception_error', 500);
        $this->backend_error_status = config('custom.status_code_for_backend_error', 500);
        $this->validation_error_status = config('custom.status_code_for_validation_error', 422);
        $this->common_error_message = config('custom.common_error_message', 'Something went wrong.');
    }

    public function contactFormSubmit(Request $request): JsonResponse
    {
        $function_name = 'contactFormSubmit';

        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:50',
                'last_name'  => 'required|string|max:50',
                'email'      => 'nullable|email|max:100',
                'phone'      => 'nullable|string|max:20',
                'service_id' => 'nullable|',
                'subject'    => 'nullable|string|max:150',
                'message'    => 'nullable|string',
            ]);

            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            $contact = ContactSubmission::create([
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
                'email'      => $request->email,
                'phone'      => $request->phone,
                'service_id' => $request->service_id,
                'subject'    => $request->subject,
                'message'    => $request->message,
            ]);

            return $this->sendResponse(
                $contact,
                'Contact form submitted successfully.',
                $this->success_status
            );

        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);

            return $this->sendError(
                $this->common_error_message,
                $this->exception_status
            );
        }
    }
}
