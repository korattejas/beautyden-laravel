<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceSubcategory;
use App\Models\ServiceCityPrice;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use App\Models\TeamMember;
use Barryvdh\DomPDF\Facade\Pdf;

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

    public function index(Request $request)
    {
        $function_name = 'index';
        try {
            $teamMembers = TeamMember::where('status', 1)->get();
            $cities = City::select('id', 'name')->get();

            $month = $request->month ?? 'all';
            $year = $request->year ?? 'all';

            $query = Appointment::query();
            if ($month != 'all') {
                $query->whereMonth('appointment_date', $month);
            }
            if ($year != 'all') {
                $query->whereYear('appointment_date', $year);
            }

            // Statistics based on filtered query
            $totalAppointments = (clone $query)->count();
            $pendingAppointments = (clone $query)->where('status', 1)->count();
            $assignedAppointments = (clone $query)->where('status', 2)->count();
            $completedAppointments = (clone $query)->where('status', 3)->count();
            $todayAppointments = (clone $query)->whereDate('appointment_date', today())->count();
            $rejectedAppointments = (clone $query)->where('status', 4)->count();

            // Calculate total revenue for filtered completed appointments
            $totalRevenue = (clone $query)->where('status', 3)->get()->sum(function ($appointment) {
                return (float) ($appointment->services_data['summary']['grand_total'] ?? 0);
            });

            // Calculate company revenue for filtered completed appointments
            $companyRevenue = (clone $query)->where('status', 3)->sum('company_amount');

            return view('admin.appointments.index', compact(
                'teamMembers',
                'cities',
                'totalAppointments',
                'pendingAppointments',
                'assignedAppointments',
                'completedAppointments',
                'totalRevenue',
                'companyRevenue',
                'todayAppointments',
                'rejectedAppointments',
                'month',
                'year'
            ));
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        try {

            $cities = City::select('id', 'name')->get();

            return view('admin.appointments.create', compact('cities'));
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function getCityServices($cityId)
    {
        $records = ServiceCityPrice::where('city_id', $cityId)
            ->where('status', 1)
            ->get();

        if ($records->isEmpty()) {
            return response()->json([]);
        }

        $categories = ServiceCategory::whereIn('id', $records->pluck('category_id'))->get();
        $subCategories = ServiceSubCategory::whereIn('id', $records->pluck('sub_category_id')->filter())->get();
        $services = Service::whereIn('id', $records->pluck('service_id'))->get();

        $data = [];

        foreach ($categories as $category) {

            $data[$category->id] = [
                'id' => $category->id,
                'name' => $category->name,
                'services' => [],
                'subcategories' => []
            ];
        }

        foreach ($records as $record) {

            $service = $services->where('id', $record->service_id)->first();
            if (!$service) continue;

            if (!$record->sub_category_id) {

                $data[$record->category_id]['services'][] = [
                    'id' => $service->id,
                    'name' => $service->name,
                    'price' => $record->price,
                    'discount_price' => $record->discount_price
                ];
            } else {

                if (!isset($data[$record->category_id]['subcategories'][$record->sub_category_id])) {

                    $sub = $subCategories->where('id', $record->sub_category_id)->first();
                    if (!$sub) continue;

                    $data[$record->category_id]['subcategories'][$record->sub_category_id] = [
                        'id' => $sub->id,
                        'name' => $sub->name,
                        'services' => []
                    ];
                }

                $data[$record->category_id]['subcategories'][$record->sub_category_id]['services'][] = [
                    'id' => $service->id,
                    'name' => $service->name,
                    'price' => $record->price,
                    'discount_price' => $record->discount_price
                ];
            }
        }

        return response()->json($data);
    }


    public function edit($id)
    {
        try {

            $appointment = Appointment::findOrFail(decryptId($id));
            $cities = City::select('id', 'name')->get();

            return view('admin.appointments.edit', compact(
                'appointment',
                'cities'
            ));
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function view($id)
    {
        try {

            $appointment = Appointment::leftJoin('cities as ct', 'ct.id', '=', 'appointments.city_id')
                ->select('appointments.*', 'ct.name as city_name')
                ->where('appointments.id', $id)
                ->firstOrFail();

            $servicesJson = $appointment->services_data ?? [];

            $client      = $servicesJson['client'] ?? [];
            $appData     = $servicesJson['appointment'] ?? [];
            $services    = $servicesJson['services'] ?? [];
            $summary     = $servicesJson['summary'] ?? [];

            $memberIds = $appointment->assigned_to
                ? explode(',', $appointment->assigned_to)
                : [];

            $memberIds = array_map('intval', $memberIds);

            $teamMembers = TeamMember::whereIn('id', $memberIds)
                ->select('name', 'experience_years')
                ->get()
                ->map(function($m) {
                    return $m->name . ' (' . ($m->experience_years ?? 0) . ' Yr Exp)';
                })
                ->toArray();

            return response()->json([
                'data' => [
                    'id'              => $appointment->id,
                    'order_number'    => $appointment->order_number,
                    'first_name'    => $appointment->first_name,
                    'last_name'    => $appointment->last_name,
                    'phone'    => $appointment->phone,
                    'email'    => $appointment->email,
                    'appointment_date'    => $appointment->appointment_date,
                    'appointment_time'    => $appointment->appointment_time,
                    'service_address'    => $appointment->service_address,
                    'special_notes'    => $appointment->special_notes,
                    'status'          => $appointment->status,
                    'company_amount'  => $appointment->company_amount,
                    'city_name'       => $appointment->city_name,
                    'created_at'      => $appointment->created_at,
                    'updated_at'      => $appointment->updated_at,

                    'client'          => $client,
                    'appointment'     => $appData,
                    'services'        => $services,
                    'summary'         => $summary,

                    'team_members'    => $teamMembers,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
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
                    )
                    ->orderBy('appointments.appointment_date', 'DESC');

                if ($request->month && $request->month != 'all') {
                    $appointments->whereMonth('appointments.appointment_date', $request->month);
                }

                if ($request->year && $request->year != 'all') {
                    $appointments->whereYear('appointments.appointment_date', $request->year);
                }

                if ($request->filter_type) {
                    if ($request->filter_type == 'today') {
                        $appointments->whereDate('appointments.appointment_date', today());
                    } elseif ($request->filter_type == 'unassigned') {
                        $appointments->where(function($q) {
                            $q->whereNull('appointments.assigned_to')->orWhere('appointments.assigned_to', '');
                        });
                    } elseif ($request->filter_type == 'pending') {
                        $appointments->where('appointments.status', 1);
                    }
                }

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

                if ($request->team_member_id) {
                    $appointments->whereRaw("FIND_IN_SET(?, appointments.assigned_to)", [$request->team_member_id]);
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
                    ->addColumn('assigned_to_name', function ($appointment) {
                        $memberNames = [];
                        if (!empty($appointment->assigned_to)) {
                            $memberIds = explode(',', $appointment->assigned_to);
                            $members = TeamMember::whereIn('id', $memberIds)->pluck('name')->toArray();
                            
                            $html = '<div class="d-flex flex-wrap gap-25">';
                            foreach($members as $name) {
                                $html .= '<span class="badge rounded-pill bg-light-primary text-primary border-primary" style="padding: 0.6em 1.2em; font-weight: 700; font-size: 0.95rem;">
                                            <i class="bi bi-person-fill me-50" style="font-size: 1.1rem;"></i> ' . $name . '
                                         </span>';
                            }
                            $html .= '</div>';
                            return $html;
                        }
                        return '<span class="badge rounded-pill bg-light-secondary text-secondary" style="padding: 0.6em 1.2em; font-size: 0.95rem;">
                                    <i class="bi bi-person-x me-50" style="font-size: 1.1rem;"></i> Not Assigned
                                </span>';
                    })
                    ->addColumn('schedule', function ($appointment) {
                        $date = \Carbon\Carbon::parse($appointment->appointment_date)->format('d-m-Y');
                        $time = !empty($appointment->appointment_time) ? \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') : '';
                        return '<div style="line-height: 1.2;"><b>'.$date.'</b><br><span class="text-muted" style="font-size: 0.95rem; font-weight: 600;">'.$time.'</span></div>';
                    })
                    ->addColumn('grand_total', function ($appointment) {
                        $grandTotal = 0;
                        if (!empty($appointment->services_data) && isset($appointment->services_data['summary']['grand_total'])) {
                            $grandTotal = $appointment->services_data['summary']['grand_total'];
                        }
                        return '<span class="fw-bolder">₹' . number_format($grandTotal, 2) . '</span>';
                    })
                    ->addColumn('company_amount', function ($appointment) {
                        return '<div class="editable-amount-wrapper" data-id="'.$appointment->id.'">
                                    <span class="amount-display fw-bolder text-primary" style="cursor: pointer; border-bottom: 1px dashed #7367f0;">₹' . number_format($appointment->company_amount ?? 0, 2) . '</span>
                                    <input type="number" step="0.01" class="form-control form-control-sm amount-input d-none" value="'.($appointment->company_amount ?? 0).'" style="width: 100px; display: inline-block;">
                                </div>';
                    })
                    ->addColumn('status', function ($appointment) {
                        $statusBadge = '';
                        switch ($appointment->status) {
                            case '1':
                                $statusBadge = '<span class="badge badge-glow bg-warning text-dark">Pending</span>';
                                break;
                            case '2':
                                $statusBadge = '<span class="badge badge-glow bg-info text-dark">Assigned</span>';
                                break;
                            case '3':
                                $statusBadge = '<span class="badge badge-glow bg-success">Completed</span>';
                                break;
                            case '4':
                                $statusBadge = '<span class="badge badge-glow bg-danger">Rejected</span>';
                                break;
                            default:
                                $statusBadge = '<span class="badge badge-glow bg-secondary">Unknown</span>';
                        }

                        $dropdown = '<div class="dropdown">
                            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-expanded="false">
                                ' . $statusBadge . '
                            </button>
                            <div class="dropdown-menu dropdown-menu-end shadow" style="background-color: #ffffff !important; z-index: 9999;">
                                <a class="dropdown-item status-change" href="javascript:void(0);" data-id="' . $appointment->id . '" data-change-status="1">
                                    <i class="bi bi-clock-history me-50 text-warning"></i>
                                    <span>Pending</span>
                                </a>
                                <a class="dropdown-item status-change" href="javascript:void(0);" data-id="' . $appointment->id . '" data-change-status="2">
                                    <i class="bi bi-person-check me-50 text-info"></i>
                                    <span>Assigned</span>
                                </a>
                                <a class="dropdown-item status-change" href="javascript:void(0);" data-id="' . $appointment->id . '" data-change-status="3">
                                    <i class="bi bi-check2-circle me-50 text-success"></i>
                                    <span>Completed</span>
                                </a>
                                <a class="dropdown-item status-change" href="javascript:void(0);" data-id="' . $appointment->id . '" data-change-status="4">
                                    <i class="bi bi-x-circle me-50 text-danger"></i>
                                    <span>Rejected</span>
                                </a>
                            </div>
                        </div>';

                        return $dropdown;
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
                            'pdf_id' => $appointment->id,
                            'current_members' => $appointment->assigned_to,
                        ];
                        return view('admin.render-view.datable-action', [
                            'action_array' => $action_array
                        ])->render();
                    })
                    ->rawColumns(['action', 'service_name', 'status', 'assigned_to_name', 'company_amount', 'grand_total', 'schedule'])
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
                // 'service_id'          => 'required|array',
                // 'service_id.*'        => 'integer|exists:services,id',
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
                'company_amount'      => 'nullable|numeric',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                logValidationException($this->controller_name, 'store', $validator);
                return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
            }

            $serviceIds = $request->input('service_id', []);
            $serviceIdsString = implode(',', $serviceIds);

            $orderNumber = '#BD' . Str::upper(Str::random(8));
            $servicesJson = json_decode($request->services_json, true);

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
                'quantity'            => $request->quantity ?? 1,
                'price'               => $request->price,
                'discount_price'      => $request->discount_price,
                'service_address'     => $request->service_address,
                'appointment_date'    => $request->appointment_date,
                'appointment_time'    => $request->appointment_time,
                'special_notes'       => $request->special_notes,
                'services_data'      => $servicesJson,
                'status'              => $request->status,
                'company_amount'      => $request->company_amount,
                //'assigned_to'         => $request->assigned_to,
                //'assigned_by'         => $request->assigned_by,
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

    public function downloadPdf($id)
    {
        try {
            $appointment = Appointment::leftJoin('cities as ct', 'ct.id', '=', 'appointments.city_id')
                ->select('appointments.*', 'ct.name as city_name')
                ->where('appointments.id', $id)
                ->firstOrFail();

            $servicesData = $appointment->services_data;
            if (is_string($servicesData)) {
                $servicesData = json_decode($servicesData, true);
            }

            $services = isset($servicesData['services']) ? $servicesData['services'] : [];
            $summary = isset($servicesData['summary']) ? $servicesData['summary'] : null;

            $memberIds = $appointment->assigned_to ? explode(',', $appointment->assigned_to) : [];
            $teamMembers = TeamMember::whereIn('id', $memberIds)->pluck('name')->toArray();

            $data = [
                'appointment'   => $appointment,
                'services'      => $services,
                'summary'       => $summary,
                'team_members'  => $teamMembers,
            ];

            $pdf = PDF::loadView('admin.appointments.pdf', $data)->setPaper('a4', 'portrait');

            $fileName = 'Invoice_'
                . ($appointment->order_number ?? 'APT')
                . '_'
                . (!empty($appointment->appointment_date)
                    ? \Carbon\Carbon::parse($appointment->appointment_date)->format('d-m-Y')
                    : date('d-m-Y'))
                . '.pdf';
            return $pdf->download($fileName);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    public function updateAmount(Request $request)
    {
        try {
            $appointment = Appointment::findOrFail($request->id);
            $appointment->update([
                'company_amount' => $request->amount
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Amount updated successfully',
                'formatted_amount' => '₹' . number_format($request->amount, 2)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update amount: ' . $e->getMessage()
            ], 500);
        }
    }
}
