<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\ContactSubmission;

class DashboardController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/DashboardController";
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $function_name = 'index';
        try {
            $totalAppointments      = Appointment::count();
            $totalAppoinmentSuccess = Appointment::where('status', 1)->count();
            $totalAppoinmentPending = Appointment::where('status', 0)->count();

            $totalContacts = ContactSubmission::count();

            return view('admin.dashboard.index', [
                'totalAppointments'      => $totalAppointments,
                'totalAppoinmentSuccess' => $totalAppoinmentSuccess,
                'totalAppoinmentPending' => $totalAppoinmentPending,
                'totalContacts'          => $totalContacts,
            ]);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }
}
