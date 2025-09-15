<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceSubcategory;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use App\Models\TeamMember;

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
            $teamMembers = TeamMember::where('status', 1)->get();
            $cities = City::select('id', 'name')->get();
            return view('admin.appointments.index', compact('teamMembers', 'cities'));
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        try {
            $teamMembers = TeamMember::all();
            $categories = ServiceCategory::where('status', 1)->get();
            $services = Service::where('status', 1)->get();
            $cities = City::select('id', 'name')->get();

            return view('admin.appointments.create', compact('teamMembers', 'categories', 'services', 'cities'));
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'create');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        try {
            $teamMembers = TeamMember::all();
            $categories = ServiceCategory::where('status', 1)->get();
            $services = Service::where('status', 1)->get();
            $appointment = Appointment::findOrFail(decryptId($id));
            $appointment->service_ids = $appointment->service_id ? explode(',', $appointment->service_id) : [];
            $cities = City::select('id', 'name')->get();

            return view('admin.appointments.edit', compact('teamMembers', 'categories', 'services', 'appointment', 'cities'));
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function view($id)
    {
        $function_name = 'view';
        try {
            $appointment = Appointment::leftJoin('service_categories as sc', 'sc.id', '=', 'appointments.service_category_id')
                ->leftJoin('service_subcategories as ssc', 'ssc.id', '=', 'appointments.service_sub_category_id')
                ->leftJoin('cities as ct', 'ct.id', '=', 'appointments.city_id')
                ->select(
                    'appointments.*',
                    'sc.name as service_category_name',
                    'ssc.name as service_sub_category_name',
                    'ct.name as city_name',
                )
                ->where('appointments.id', $id)
                ->firstOrFail();

            $serviceIds = $appointment->service_id;
            $serviceIds = $serviceIds ? explode(',', $serviceIds) : [];
            $serviceIds = array_map('intval', $serviceIds);
            $services = Service::whereIn('id', $serviceIds)->pluck('name')->toArray();

            $memberIds = $appointment->assigned_to;
            $memberIds = $memberIds ? explode(',', $memberIds) : [];
            $memberIds = array_map('intval', $memberIds);
            $teamMembers = TeamMember::whereIn('id', $memberIds)->pluck('name')->toArray();


            return response()->json([
                'data' => [
                    'id'                  => $appointment->id,
                    'order_number'        => $appointment->order_number,
                    'first_name'          => $appointment->first_name,
                    'last_name'           => $appointment->last_name,
                    'email'               => $appointment->email,
                    'phone'               => $appointment->phone,
                    'quantity'            => $appointment->quantity,
                    'price'               => $appointment->price,
                    'discount_price'      => $appointment->discount_price,
                    'service_address'     => $appointment->service_address,
                    'appointment_date'    => $appointment->appointment_date,
                    'appointment_time'    => $appointment->appointment_time,
                    'special_notes'       => $appointment->special_notes,
                    'status'              => $appointment->status,
                    'assigned_by'         => $appointment->assigned_by,
                    'assigned_to'         => $appointment->assigned_to,
                    'created_at'          => $appointment->created_at,
                    'updated_at'          => $appointment->updated_at,

                    'service_category'    => $appointment->service_category_name,
                    'service_sub_category'    => $appointment->service_sub_category_name,
                    'city_name'           => $appointment->city_name,
                    'services'            => $services,
                    'team_members'        => $teamMembers,
                ]
            ], 200);
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
                    ->leftJoin('cities as c', 'appointments.city_id', '=', 'c.id')
                    ->select(
                        'appointments.*',
                        'sc.name as service_category_name'
                    );

                if ($request->status !== null && $request->status !== '') {
                    $appointments->where('appointments.status', $request->status);
                }

                if ($request->appointment_date) {
                    $appointments->whereDate('appointments.appointment_date', $request->appointment_date);
                }

                if ($request->appointment_time) {
                    $appointments->whereTime('appointments.appointment_time', $request->appointment_time);
                }

                if ($request->created_date) {
                    $appointments->whereDate('appointments.created_at', $request->created_date);
                }

                if ($request->city_id) {
                    $appointments->where('appointments.city_id', $request->city_id);
                }

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
                        switch ($appointment->status) {
                            case '1':
                                return '<span class="badge badge-glow bg-warning text-dark">Pending</span>';
                            case '2':
                                return '<span class="badge badge-glow bg-info text-dark">Assigned</span>';
                            case '3':
                                return '<span class="badge badge-glow bg-success">Completed</span>';
                            case '4':
                                return '<span class="badge badge-glow bg-danger">Rejected</span>';
                            default:
                                return '<span class="badge badge-glow bg-secondary">Unknown</span>';
                        }
                    })
                    ->addColumn('action', function ($appointment) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'delete_id' => $appointment->id,
                            'edit_route' => route('admin.appointments.edit', encryptId($appointment->id)),
                            // 'current_status' => $appointment->status,
                            'hidden_id' => $appointment->id,
                            'assign_id' => $appointment->id,
                            'view_id' => $appointment->id,
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

    public function store(Request $request)
    {
        $id = $request->input('edit_value', 0);

        try {
            $rules = [
                'city_id'             => 'required|integer|exists:cities,id',
                'service_category_id' => 'nullable|integer|exists:service_categories,id',
                'service_id'          => 'required|array',
                'service_id.*'        => 'integer|exists:services,id',
                'first_name'          => 'required|string|max:50',
                'last_name'           => 'nullable|string|max:50',
                'email'               => 'nullable|email|max:100',
                'phone'               => 'nullable|string|max:20',
                'price'               => 'nullable|numeric',
                'discount_price'      => 'nullable|numeric',
                'service_address'     => 'nullable|string|max:255',
                'appointment_date'    => 'nullable|date',
                'appointment_time'    => 'nullable',
                'special_notes'       => 'nullable|string',
                'status'              => 'required|in:1,2,3,4', // 1=Pending, 2=Assigned, 3=Completed, 4=Rejected
                'assigned_to'         => 'nullable|string|max:150',
                'assigned_by'         => 'nullable|string|max:100',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                logValidationException($this->controller_name, 'store', $validator);
                return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
            }

            $serviceIds = $request->input('service_id', []);
            $serviceIdsString = implode(',', $serviceIds);

            $orderNumber = '#BEAUTYDEN' . Str::upper(Str::random(8));

            $data = [
                'city_id' => $request->city_id,
                'service_category_id' => $request->service_category_id,
                'service_sub_category_id' => $request->service_sub_category_id,
                'service_id'          => $serviceIdsString,
                'order_number'        => $orderNumber,
                'first_name'          => $request->first_name,
                'last_name'           => $request->last_name,
                'email'               => $request->email,
                'phone'               => $request->phone,
                'quantity'            => $request->quantity,
                'price'               => $request->price,
                'discount_price'      => $request->discount_price,
                'service_address'     => $request->service_address,
                'appointment_date'    => $request->appointment_date,
                'appointment_time'    => $request->appointment_time,
                'special_notes'       => $request->special_notes,
                'status'              => $request->status,
                'assigned_to'         => $request->assigned_to,
                'assigned_by'         => $request->assigned_by,
            ];

            if ($id == 0) {
                Appointment::create($data);
                return response()->json(['success' => true, 'message' => "Appointment added successfully"]);
            } else {
                Appointment::where('id', $id)->update($data);
                return response()->json(['success' => true, 'message' => "Appointment updated successfully"]);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'store');
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

    public function AssignMember(Request $request)
    {
        $function_name = 'AssignMember';
        try {
            $memberString = implode(',', $request->members);
            Appointment::where('id', $request->value_id)->update([
                'assigned_to' => $memberString,
                'assigned_by' => 'Admin',
                'status' => 2
            ]);
            return response()->json(['message' => 'Team member assign successfully']);
        } catch (\Exception $e) {
            logger()->error("$function_name: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getSubcategories($categoryId)
    {
        try {
            $subcategories = ServiceSubcategory::where('service_category_id', $categoryId)
                ->where('status', 1)
                ->get();

            return response()->json($subcategories);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to sub category data');
        }
    }
}
