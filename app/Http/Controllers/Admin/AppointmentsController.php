<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Service;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class AppointmentsController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/AppointmentsController";
    }

    public function index()
    {
        $function_name = 'index';
        try {
            return view('admin.appointments.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataAppointments(Request $request)
    {
        $function_name = 'getDataAppointments';
        try {
            if ($request->ajax()) {
                $appointments = Appointment::query()
                    ->leftJoin('service_categories as sc', 'sc.id', '=', 'appointments.service_category_id')
                    ->select(
                        'appointments.*',
                        'sc.name as service_category_name'
                    );

                return DataTables::of($appointments)
                    ->addColumn('service_name', function ($appointment) {
                        $serviceNames = [];
                        if (!empty($appointment->service_id)) {
                            $serviceIds = explode(',', $appointment->service_id);
                            $services = Service::whereIn('id', $serviceIds)->pluck('name')->toArray();
                            $serviceNames = $services;
                        }
                        return implode(', ', $serviceNames);
                    })
                    ->addColumn('status', function ($appointment) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => $appointment->status
                        ];
                        return view('admin.render-view.datable-label', [
                            'status_array' => $status_array
                        ])->render();
                    })
                    ->addColumn('action', function ($appointment) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'delete_id' => $appointment->id,
                            'current_status' => $appointment->status,
                            'hidden_id' => $appointment->id,
                        ];
                        return view('admin.render-view.datable-action', [
                            'action_array' => $action_array
                        ])->render();
                    })
                    ->rawColumns(['action', 'service_name', 'status'])
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
            Appointment::where('id', $id)->update(['status' => $status]);
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
            $appointment = Appointment::where('id', $id)->first();
            if ($appointment) {
                $appointment->delete();

                return response()->json([
                    'message' => 'Appointment deleted successfully'
                ]);
            } else {
                logger()->error("$function_name: Failed to delete appointment not found.");
                return response()->json(['error' => 'Failed to delete appointment not found.'], 500);
            }
        } catch (\Exception $e) {
            logger()->error("$function_name: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }
}
